<?php

namespace App\Form;

use App\Entity\Camion_remorquage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CamionRemorquageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_agence', TextType::class, [
                'label' => 'Nom Agence',
                'required' => true,
                'empty_data' => '',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('modele', ChoiceType::class, [
                'label' => 'Modele',
                'choices' => [
                    'Standard' => 'Standard',
                    'XL' => 'XL'
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('annee', NumberType::class, [
                'label' => 'Année',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1990,
                    'max' => (new \DateTime())->format('Y')
                ],
                'required' => true
            ])
            ->add('num_tel', TextType::class, [
                'label' => 'Numéro Téléphone',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'Disponible' => 'Disponible',
                    'Non Disponible' => 'Non Disponible'
                ],
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