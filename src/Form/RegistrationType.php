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
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prenom', TextType::class, [
                'label' => 'Votre prénom',
                'constraints' => [
                    new NotBlank(['message' => 'Le prénom est obligatoire.']),
                    new Length(['max' => 45, 'maxMessage' => 'Le prénom ne peut pas dépasser 45 caractères.']),
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Votre nom',
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est obligatoire.']),
                    new Length(['max' => 45, 'maxMessage' => 'Le nom ne peut pas dépasser 45 caractères.']),
                ],
            ])
            ->add('cin', TextType::class, [
                'label' => 'Votre CIN',
                'constraints' => [
                    new NotBlank(['message' => 'Le CIN est obligatoire.']),
                    new Length([
                        'min' => 8,
                        'max' => 8,
                        'exactMessage' => 'Le CIN doit contenir exactement 8 chiffres.',
                    ]),
                    new \Symfony\Component\Validator\Constraints\Regex([
                        'pattern' => '/^\d{8}$/',
                        'message' => 'Le CIN doit contenir uniquement des chiffres.',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre email',
                'constraints' => [
                    new NotBlank(['message' => 'L\'email est obligatoire.']),
                    new \Symfony\Component\Validator\Constraints\Email(['message' => 'L\'email doit être valide.']),
                    new Length(['max' => 45, 'maxMessage' => 'L\'email ne peut pas dépasser 45 caractères.']),
                ],
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Votre address',
                'constraints' => [
                    new NotBlank(['message' => 'L\'adresse est obligatoire.']),
                    new \Symfony\Component\Validator\Constraints\Regex([
                        'pattern' => '/^\d+\sRue\s.*$/',
                        'message' => 'L\'adresse doit contenir un numéro suivi d\'un nom de rue valide.',
                    ]),
                    new Length(['max' => 255, 'maxMessage' => 'L\'adresse ne peut pas dépasser 255 caractères.']),
                ],
            ])
            ->add('username', TextType::class, [
                'label' => 'Votre username',
                'constraints' => [
                    new NotBlank(['message' => 'Le nom d\'utilisateur est obligatoire.']),
                    new Length(['max' => 255, 'maxMessage' => 'Le nom d\'utilisateur ne peut pas dépasser 255 caractères.']),
                ],
            ])
            ->add('tel', TextType::class, [
                'label' => 'Votre numéro de téléphone',
                'constraints' => [
                    new NotBlank(['message' => 'Le téléphone est obligatoire.']),
                    new Length([
                        'min' => 8,
                        'max' => 8,
                        'exactMessage' => 'Le téléphone doit contenir exactement 8 chiffres.',
                    ]),
                    new \Symfony\Component\Validator\Constraints\Regex([
                        'pattern' => '/^\d{8}$/',
                        'message' => 'Le téléphone doit contenir uniquement des chiffres.',
                    ]),
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Votre password',
                'constraints' => [
                    new NotBlank(['message' => 'Le mot de passe est obligatoire.']),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit contenir au moins 6 caractères.',
                        'max' => 255,
                        'maxMessage' => 'Le mot de passe ne peut pas dépasser 255 caractères.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}