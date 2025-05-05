<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\SecurityQuestionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityQuestionController extends AbstractController
{
    #[Route('/security-question/{userId}', name: 'app_security_question')]
    public function index(int $userId, Request $request, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->find($userId);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $form = $this->createForm(SecurityQuestionType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setQuestion($form->get('question')->getData());
            $user->setReponse($form->get('reponse')->getData());

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security_question/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}