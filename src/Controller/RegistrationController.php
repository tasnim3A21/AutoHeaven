<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        $user = new User();
        
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        
        // Set default values for the user
        $user->setRole('client');
        $user->setBan('non');
        $user->setPhoto_profile('default.jpg');
        $user->setQuestion('default');
        $user->setReponse('default');
        
        $suggestedUsername = null;
        $usernameTaken = false;

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // Form is valid, proceed with registration
                $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
                $user->setPassword($hashedPassword);
        
                $em->persist($user);
                $em->flush();
        
                // Redirect to security question page with user ID
                return $this->redirectToRoute('app_security_question', ['userId' => $user->getId()]);
            } else {
                // Check if the username is the reason for the validation failure
                $errors = $form->get('username')->getErrors(true);
                foreach ($errors as $error) {
                    if ($error->getMessage() === 'Cet username existe déjà.') {
                        $usernameTaken = true;
                        $suggestedUsername = $this->suggestUsername($user->getPrenom(), $user->getNom(), $user->getUsername());
                        break;
                    }
                }
            }
        }
        
        return $this->render('signup/index.html.twig', [
            'form' => $form->createView(),
            'username_taken' => $usernameTaken,
            'suggested_username' => $suggestedUsername,
        ]);
    }

    #[Route('/regenerate-username', name: 'app_regenerate_username', methods: ['POST'])]
    public function regenerateUsername(Request $request): JsonResponse
    {
        $rawContent = $request->getContent();
        error_log('Raw request content: ' . $rawContent);

        $content = json_decode($rawContent, true);
        if ($content === null) {
            error_log('Error: Failed to parse JSON request body');
            return new JsonResponse(['error' => 'Invalid JSON payload'], 400);
        }

        $prenom = $content['prenom'] ?? '';
        $nom = $content['nom'] ?? '';
        $currentUsername = $content['currentUsername'] ?? '';

        error_log('Regenerate username request received. Prenom: ' . $prenom . ', Nom: ' . $nom . ', Current Username: ' . $currentUsername);

        if (!$prenom || !$nom) {
            error_log('Error: First name and last name are required');
            return new JsonResponse(['error' => 'First name and last name are required'], 400);
        }

        $newUsername = $this->suggestUsername($prenom, $nom, $currentUsername);

        error_log('Generated username: ' . $newUsername);

        return new JsonResponse(['username' => $newUsername]);
    }

    private function suggestUsername(string $prenom, string $nom, string $excludeUsername = ''): string
    {
        $baseUsername = strtolower(preg_replace('/\s+/', '', $prenom) . '.' . preg_replace('/\s+/', '', $nom));
        $suggestedUsername = $baseUsername;
        $counter = 1;

        while ($this->userRepository->findOneBy(['username' => $suggestedUsername]) && $suggestedUsername !== $excludeUsername) {
            $suggestedUsername = $baseUsername . $counter;
            $counter++;
        }

        if ($suggestedUsername === $excludeUsername) {
            $suggestedUsername = $baseUsername . $counter;
            while ($this->userRepository->findOneBy(['username' => $suggestedUsername])) {
                $counter++;
                $suggestedUsername = $baseUsername . $counter;
            }
        }

        return $suggestedUsername;
    }
}