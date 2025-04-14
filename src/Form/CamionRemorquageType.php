<?php

namespace App\Form;

use App\Entity\Camion_remorquage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CamionRemorquageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('modele', TextType::class, [
                'label' => 'Modele',
                'attr' => ['class' => 'form-control']
            ])
            ->add('annee', NumberType::class, [
                'label' => 'Année',
                'attr' => ['class' => 'form-control']
            ])
            ->add('num_tel', TextType::class, [
                'label' => 'Numéro Téléphone',
                'attr' => [
                    'class' => 'form-control',
                    'maxlength' => 8
                ]
            ])
            ->add('statut', TextType::class, [
                'label' => 'Statut',
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Camion_remorquage::class,
        ]);
    }
}