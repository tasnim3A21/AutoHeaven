<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function editProfile(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
    
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $uploadedFile = $form->get('profile_image')->getData();
    
                // Handle the image upload if the user selected a new one
                if ($uploadedFile) {
                    $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();
    
                    try {
                        $uploadedFile->move(
                            $this->getParameter('profile_directory'),
                            $newFilename
                        );
                        $user->setPhoto_profile($newFilename);
                    } catch (FileException $e) {
                        $this->addFlash('danger', 'Upload failed: ' . $e->getMessage());
                    }
                }

                // Persist changes (including the email, username, etc.)
                $entityManager->flush();
                $this->addFlash('success', 'Profile updated successfully!');
                return $this->redirectToRoute('app_profile');
            } else {
                // If form is invalid, we want to show a flash message
                $this->addFlash('danger', 'There were errors with your submission.');
            }
        }
    
        return $this->render('profile/index.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
