<?php

namespace App\Form;

use App\Entity\Camion_Remorquage;
use App\Entity\Res_remorquage;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResRemorquageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('camionRemorquage', EntityType::class, [
                'class' => Camion_Remorquage::class,
                'choice_label' => function (Camion_Remorquage $camion) {
                    return $camion->getNomAgence() . ' ID : ' . $camion->getId_cr();
                },
                'label' => 'Camion de Remorquage',
                'attr' => ['class' => 'form-control'],
                'placeholder' => 'Sélectionnez un camion',
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => function(User $user) {
                    return $user->getNom() . ' ' . $user->getPrenom() . ' ID : ' . $user->getId();
                },
                'label' => 'Client',
                'attr' => ['class' => 'form-control'],
                'placeholder' => 'Sélectionnez un client',
            ])
            ->add('point_ramassage', TextType::class, [
                'label' => 'Point de ramassage',
                'attr' => ['class' => 'form-control']
            ])
            ->add('point_depot', TextType::class, [
                'label' => 'Point de dépôt',
                'attr' => ['class' => 'form-control']
            ])
            ->add('date', DateType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Res_remorquage::class,
        ]);
    }
}