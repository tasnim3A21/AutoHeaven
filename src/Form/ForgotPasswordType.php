<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ForgotPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => ['placeholder' => ' '],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez entrer votre email.']),
                    new Assert\Email(['message' => 'Veuillez entrer un email valide.']),
                ],
            ])
            ->add('phone', TelType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Entrez votre numéro de téléphone (ex. +1234567890)'],
                'required' => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^\+[1-9]\d{1,14}$/',
                        'message' => 'Veuillez entrer un numéro de téléphone valide au format E.164 (ex. +1234567890).',
                    ]),
                ],
            ])
            ->add('security_question', ChoiceType::class, [
                'label' => false,
                'attr' => ['placeholder' => ' '],
                'choices' => [
                    'Sélectionnez une question' => '',
                    'Dans quelle ville êtes-vous né(e) ?' => 'birth_city',
                    'Quel est le nom de votre premier animal de compagnie ?' => 'first_pet',
                    'Quel est le nom de votre professeur préféré ?' => 'favorite_teacher',
                ],
                'required' => false,
            ])
            ->add('security_answer', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Entrez votre réponse'],
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer une réponse.',
                        'groups' => ['security_question'],
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'validation_groups' => ['Default'],
        ]);
    }
}