<?php
namespace App\Controller;

use App\Form\ForgotPasswordType;
use App\Form\VerifyOtpType;
use App\Form\ResetPasswordType;
use App\Form\LoginType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twilio\Rest\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class SecurityController extends AbstractController
{
    private $userRepository;
    private $entityManager;
    private $passwordHasher;
    private $requestStack;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        RequestStack $requestStack
    ) {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->requestStack = $requestStack;
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        $form = $this->createForm(LoginType::class);
        $form->handleRequest($request);

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $isBanned = false;
        $banMessage = null;
        if ($lastUsername) {
            $user = $this->userRepository->findOneBy(['email' => $lastUsername]);
            if ($user && $user->getBan() === 'oui') {
                $isBanned = true;
                $banMessage = 'Vous êtes définitivement banni du site.';
            }
        }

        if ($request->query->has('oauth')) {
            $session = $request->getSession();
            $session->invalidate();
        }

        return $this->render('login/index.html.twig', [
            'form' => $form->createView(),
            'last_username' => $lastUsername ?: ($form->getData()['email'] ?? ''),
            'error' => $error,
            'google_recaptcha_site_key' => $this->getParameter('google_recaptcha_site_key'),
            'is_banned' => $isBanned,
            'ban_message' => $banMessage,
        ]);
    }

    #[Route(path: '/reset', name: 'app_password')]
    public function forgotPassword(Request $request, RateLimiterFactory $smsLimiter, RateLimiterFactory $securityQuestionLimiter): Response
    {
        $form = $this->createForm(ForgotPasswordType::class, null, [
            'validation_groups' => ['Default'],
        ]);
        $form->handleRequest($request);

        $error = null;
        $session = $this->requestStack->getSession();

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $resetMethod = $request->request->get('reset_method');
            $user = $this->userRepository->findOneBy(['email' => $data['email']]);

            if (!$user) {
                $this->addFlash('success', 'Si l\'email existe, un code de réinitialisation a été envoyé.');
                return $this->redirectToRoute('app_password');
            }

            if ($resetMethod === 'sms' && $data['phone']) {
                $smsLimiter = $smsLimiter->create($request->getClientIp() . '_' . $data['email']);
                if (false === $smsLimiter->consume(1)->isAccepted()) {
                    $error = 'Trop de tentatives SMS. Réessayez plus tard.';
                    return $this->render('resetpswd/index.html.twig', [
                        'form' => $form->createView(),
                        'error' => $error,
                    ]);
                }

                $otp = random_int(100000, 999999);
                $session->set('reset_otp', [
                    'otp' => (string)$otp,
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'expires_at' => (new \DateTime('+10 minutes'))->getTimestamp(),
                    'is_used' => false,
                ]);

                try {
                    $twilio = new Client(
                        $this->getParameter('twilio_account_sid'),
                        $this->getParameter('twilio_auth_token')
                    );
                    $twilio->messages->create(
                        $data['phone'],
                        [
                            'from' => $this->getParameter('twilio_phone_number'),
                            'body' => "Votre code de réinitialisation AutoHeaven est : $otp. Il expire dans 10 minutes."
                        ]
                    );
                    $this->addFlash('success', 'Un code de réinitialisation a été envoyé à votre téléphone.');
                    return $this->redirectToRoute('app_verify_otp', ['email' => $data['email']]);
                } catch (\Exception $e) {
                    $error = 'Échec de l\'envoi du SMS. Veuillez entrer un numéro de téléphone valide (ex. +1234567890).';
                    error_log('Twilio error: ' . $e->getMessage());
                }
            } elseif ($resetMethod === 'question' && $data['security_question'] && $data['security_answer']) {
                $limiter = $securityQuestionLimiter->create($request->getClientIp() . '_' . $data['email']);
                if (false === $limiter->consume(1)->isAccepted()) {
                    $error = 'Trop de tentatives. Réessayez plus tard.';
                    return $this->render('resetpswd/index.html.twig', [
                        'form' => $form->createView(),
                        'error' => $error,
                    ]);
                }

                // Mapper la question soumise au texte brut
                $questionMap = [
                    'birth_city' => 'Dans quelle ville êtes-vous né(e) ?',
                    'first_pet' => 'Quel est le nom de votre premier animal de compagnie ?',
                    'favorite_teacher' => 'Quel est le nom de votre professeur préféré ?',
                ];
                $submittedQuestion = $questionMap[$data['security_question']] ?? $data['security_question'];

                // Débogage : Afficher les valeurs comparées
                dump(
                    'Stored Question: ' . ($user->getQuestion() ?? 'N/A'),
                    'Submitted Question: ' . $submittedQuestion,
                    'Stored Answer: ' . ($user->getReponse() ?? 'N/A'),
                    'Submitted Answer: ' . $data['security_answer'],
                    'Processed Stored Answer: ' . strtolower(trim($user->getReponse() ?? '')),
                    'Processed Submitted Answer: ' . strtolower(trim($data['security_answer'] ?? ''))
                );

                // Vérifier la question et la réponse
                if ($user->getQuestion() === $submittedQuestion && strtolower(trim($user->getReponse())) === strtolower(trim($data['security_answer']))) {
                    $session->set('security_reset_verified', [
                        'email' => $data['email'],
                        'verified_at' => time(),
                    ]);
                    $this->addFlash('success', 'Réponse correcte. Vous pouvez réinitialiser votre mot de passe.');
                    return $this->redirectToRoute('app_reset_password', ['email' => $data['email']]);
                } else {
                    $error = 'Question ou réponse incorrecte.';
                }
            } else {
                $error = 'Méthode de réinitialisation invalide ou champs manquants.';
            }
        }

        return $this->render('resetpswd/index.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
        ]);
    }

    #[Route(path: '/verify-otp/{email}', name: 'app_verify_otp')]
    public function verifyOtp(string $email, Request $request): Response
    {
        $form = $this->createForm(VerifyOtpType::class);
        $form->handleRequest($request);

        $error = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $otp = $data['otp'];
            $user = $this->userRepository->findOneBy(['email' => $email]);
            $session = $this->requestStack->getSession();
            $otpData = $session->get('reset_otp');

            if (!$user) {
                $error = 'Utilisateur non trouvé.';
            } elseif (!$otpData || $otpData['email'] !== $email || $otpData['otp'] !== $otp) {
                $error = 'Code OTP invalide ou expiré.';
            } elseif ($otpData['is_used']) {
                $error = 'Ce code OTP a déjà été utilisé.';
            } elseif ($otpData['expires_at'] < time()) {
                $error = 'Le code OTP a expiré.';
            } else {
                $otpData['is_used'] = true;
                $session->set('reset_otp', $otpData);
                $this->addFlash('success', 'Code OTP vérifié. Vous pouvez réinitialiser votre mot de passe.');
                return $this->redirectToRoute('app_reset_password', ['email' => $email]);
            }
        }

        return $this->render('resetpswd/verify_otp.html.twig', [
            'form' => $form->createView(),
            'email' => $email,
            'error' => $error,
        ]);
    }

    #[Route(path: '/reset-password/{email}', name: 'app_reset_password')]
    public function resetPassword(string $email, Request $request): Response
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if (!$user) {
            $this->addFlash('error', 'Utilisateur non trouvé.');
            return $this->redirectToRoute('app_password');
        }

        $session = $this->requestStack->getSession();
        $otpData = $session->get('reset_otp');
        $securityResetVerified = $session->get('security_reset_verified');

        if (!$otpData && !$securityResetVerified) {
            $this->addFlash('error', 'Vérification non effectuée.');
            return $this->redirectToRoute('app_password');
        }

        if ($otpData && ($otpData['email'] !== $email || !$otpData['is_used'])) {
            $this->addFlash('error', 'Code OTP invalide ou non vérifié.');
            return $this->redirectToRoute('app_password');
        }

        if ($securityResetVerified && ($securityResetVerified['email'] !== $email || $securityResetVerified['verified_at'] < time() - 600)) {
            $this->addFlash('error', 'Vérification de la question de sécurité expirée.');
            return $this->redirectToRoute('app_password');
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        $error = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $session->remove('reset_otp');
            $session->remove('security_reset_verified');
            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('resetpswd/reset_password.html.twig', [
            'form' => $form->createView(),
            'email' => $email,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}