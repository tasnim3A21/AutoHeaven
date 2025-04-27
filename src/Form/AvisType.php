<?php
namespace App\Form;

use App\Entity\Avis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('commentaire', TextareaType::class, [
                'label' => 'Commentaire',
                'required' => true,
                'attr' => ['rows' => 4, 'placeholder' => 'Votre avis...']
            ])
            ->add('note', ChoiceType::class, [
                'label' => 'Note de satisfaction',
                'choices' => [
                    '⭐ - Non satisfait' => 1,
                    '⭐⭐ - Peu satisfait' => 2,
                    '⭐⭐⭐ - Moyennement satisfait' => 3,
                    '⭐⭐⭐⭐ - Satisfait' => 4,
                ],
                'expanded' => true,  // radio boutons (étoiles)
                'multiple' => false,
                'required' => true
            ])
            ->add('dateavis', DateType::class, [
                'label' => 'Date de l\'avis',
                'widget' => 'single_text',
                'required' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
        ]);
    }
}
