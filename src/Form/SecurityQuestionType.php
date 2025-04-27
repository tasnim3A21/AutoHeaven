<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SecurityQuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('question', ChoiceType::class, [
                'label' => 'Sélectionnez une question',
                'choices' => [
                    'Sélectionnez une question' => '',
                    'Dans quelle ville êtes-vous né(e) ?' => 'Dans quelle ville êtes-vous né(e) ?',
                    'Quel est le nom de votre premier animal de compagnie ?' => 'Quel est le nom de votre premier animal de compagnie ?',
                    'Quel est le nom de votre professeur préféré ?' => 'uel est le nom de votre professeur préféré ?',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner une question.']),
                ],
            ])
            ->add('reponse', TextType::class, [
                'label' => 'Votre réponse',
                'constraints' => [
                    new NotBlank(['message' => 'La réponse est obligatoire.']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}