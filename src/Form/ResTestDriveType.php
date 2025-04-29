<?php

namespace App\Form;

use App\Entity\Res_testdrive;
use App\Entity\User;
use App\Entity\Voiture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResTestDriveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getNom() . ' ' . $user->getPrenom() . ' : ID ' . $user->getId();
                },
                'label' => 'Client',
                'attr' => ['class' => 'form-control'],
                'placeholder' => 'Sélectionnez un client',
            ])

            ->add('voiture', EntityType::class, [
                'class' => Voiture::class,
                'choice_label' => function (Voiture $voiture) {
                    return $voiture->getMarque() . ' : ID ' . $voiture->getId_v();
                },
                'label' => 'Voiture',
                'attr' => ['class' => 'form-control'],
                'placeholder' => 'Sélectionnez une voiture',
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