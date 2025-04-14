<?php

namespace App\Form;

use App\Entity\Res_testdrive;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResTestDriveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id_u', NumberType::class, [
                'label' => 'ID Utilisateur',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'placeholder' => 'Entrez votre ID utilisateur'
                ],
                'required' => true
            ])
            ->add('id_v', NumberType::class, [
                'label' => 'ID Véhicule',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'placeholder' => 'Entrez l\'ID du véhicule'
                ],
                'required' => true
            ])
            ->add('date', DateType::class, [
                'label' => 'Date du test drive',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                    'min' => (new \DateTime())->format('Y-m-d')
                ],
                'required' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Res_testdrive::class,
            'csrf_protection' => true,
        ]);
    }
}