<?php
namespace App\Controller;

use App\Entity\User; // Assuming you have a User entity
use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;  // <-- Correct namespace


    class RegistrationController extends AbstractController
    {
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
            
            if ($form->isSubmitted() && $form->isValid()) {
                // Hash the password before saving
                $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
                $user->setPassword($hashedPassword);
        
        
                // Persist the user entity to the database
                $em->persist($user);
                $em->flush();
        
                // Redirect after successful registration
                return $this->redirectToRoute('app_login');
            }
        
            return $this->render('signup/index.html.twig', [
                'form' => $form->createView(),
            ]);
        }
        
    }