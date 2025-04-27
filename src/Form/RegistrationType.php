<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prenom', TextType::class, [
                'label' => 'Votre prénom',
                'constraints' => [
                    new NotBlank(['message' => 'Please enter your first name']),
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Votre nom',
                'constraints' => [
                    new NotBlank(['message' => 'Please enter your last name']),
                ],
            ])
            ->add('cin', TextType::class, [
                'label' => 'Votre CIN',
                'constraints' => [
                    new NotBlank(['message' => 'Please enter your CIN']),
                    new Length([
                        'min' => 8,
                        'max' => 8,
                        'exactMessage' => 'Le CIN doit contenir exactement 8 chiffres.',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre email',
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a valid email']),
                ],
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Votre address',
                'constraints' => [
                    new NotBlank(['message' => 'Please enter your address']),
                ],
            ])
            ->add('username', TextType::class, [
                'label' => 'Votre username',
                'constraints' => [
                    new NotBlank(['message' => 'Please choose a username']),
                ],
            ])
            ->add('tel', TextType::class, [
                'label' => 'Votre numéro de téléphone',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre numéro de téléphone']),
                    new Length([
                        'min' => 8,
                        'max' => 8,
                        'exactMessage' => 'Le numéro doit contenir exactement 8 chiffres.',
                    ]),
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Votre password',
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a password']),
                    new Length(['min' => 6, 'minMessage' => 'Mdp doit contenir au moins 6 caractères.']),
                ],
            ]);
    }
}
