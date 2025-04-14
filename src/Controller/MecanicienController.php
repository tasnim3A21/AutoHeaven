<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\MecanicienType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\FormError;


final class MecanicienController extends AbstractController
{
    #[Route('/mecanicien', name: 'app_mecanicien')]
    public function showMecaniciens(EntityManagerInterface $em): Response
{
    // Récupérer les mécaniciens
    $mecaniciens = $em->getRepository(User::class)->findBy(['role' => 'mecanicien']);

    return $this->render('mecanicien/index.html.twig', [
        'mecaniciens' => $mecaniciens,
    ]);
}


#[Route('/mecanicien/ajout', name: 'ajout_mecanicien')]
public function ajouterMecanicien(
    Request $request,
    EntityManagerInterface $em,
    UserPasswordHasherInterface $passwordHasher
): Response {
    $mecanicien = new User();

    // Create the form
    $form = $this->createForm(MecanicienType::class, $mecanicien);
    $form->handleRequest($request);

    // Check if form is submitted and valid
    if ($form->isSubmitted() && $form->isValid()) {
        // Check if the email already exists
        $existingEmail = $em->getRepository(User::class)->findOneBy(['email' => $mecanicien->getEmail()]);
        if ($existingEmail) {
            $form->get('email')->addError(new FormError('Cette adresse email est déjà utilisée.'));
        }

        // Check if the username already exists
        $existingUsername = $em->getRepository(User::class)->findOneBy(['username' => $mecanicien->getUsername()]);
        if ($existingUsername) {
            $form->get('username')->addError(new FormError('Ce nom d\'utilisateur est déjà pris.'));
        }
        

        // If there are errors, do not proceed with saving
        if ($form->isValid()) {
            // Hash the password before saving
            $hashedPassword = $passwordHasher->hashPassword($mecanicien, $mecanicien->getPassword());
            $mecanicien->setPassword($hashedPassword);
            $mecanicien->setRole('mecanicien');

            // Set default values if necessary
            $mecanicien->setBan('non');
            $mecanicien->setPhoto_profile('default-photo.png');
            $mecanicien->setQuestion('default');
            $mecanicien->setReponse('default');

            // Persist the mecanicien entity to the database
            $em->persist($mecanicien);
            $em->flush();

            // Redirect after successful addition
            return $this->redirectToRoute('app_mecanicien');
        }
    }

    // Render the form in the view
    return $this->render('mecanicien/ajout.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('/mecanicien/modifier/{id}', name: 'modifier_mecanicien')]
public function modifierMecanicien(int $id, Request $request, EntityManagerInterface $em): Response
{
    // Find the mechanic by id
    $mecanicien = $em->getRepository(User::class)->find($id);
    if (!$mecanicien) {
        // If no mechanic found, throw a 404 exception
        throw $this->createNotFoundException('Mécanicien non trouvé');
    }

    // Create the form for editing the mechanic
    $form = $this->createForm(MecanicienType::class, $mecanicien);

    // Handle form submission
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        // Check if the email already exists and is not the same as the current email
        $existingEmail = $em->getRepository(User::class)->findOneBy(['email' => $mecanicien->getEmail()]);
        if ($existingEmail && $existingEmail->getId() !== $mecanicien->getId()) {
            $form->get('email')->addError(new FormError('Cette adresse email est déjà utilisée.'));
        }

        // Check if the username already exists and is not the same as the current username
        $existingUsername = $em->getRepository(User::class)->findOneBy(['username' => $mecanicien->getUsername()]);
        if ($existingUsername && $existingUsername->getId() !== $mecanicien->getId()) {
            $form->get('username')->addError(new FormError('Ce nom d\'utilisateur est déjà pris.'));
        }

        // If no errors, flush the changes to the database
        if ($form->isValid()) {
            $em->flush();
            // Redirect to the list of mechanics
            return $this->redirectToRoute('app_mecanicien');
        }
    }

    // Render the form
    return $this->render('mecanicien/modifier.html.twig', [
        'form' => $form->createView(),
    ]);
}
// Delete mechanic method
#[Route('/mecanicien/supprimer/{id}', name: 'supprimer_mecanicien')]
public function supprimerMecanicien(int $id, EntityManagerInterface $em): Response
{
    // Find the mechanic by id
    $mecanicien = $em->getRepository(User::class)->find($id);
    if (!$mecanicien) {
        // If no mechanic found, throw a 404 exception
        throw $this->createNotFoundException('Mécanicien non trouvé');
    }

    // Remove the mechanic from the database
    $em->remove($mecanicien);
    $em->flush();

    // Redirect to the list of mechanics
    return $this->redirectToRoute('app_mecanicien');
}
}
