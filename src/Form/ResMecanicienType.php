<?php

namespace App\Form;

use App\Entity\Res_mecanicien;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResMecanicienType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('client', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getNom() . ' ' . $user->getPrenom() . ' : ID ' . $user->getId();
                },
                'label' => 'Client',
                'attr' => ['class' => 'form-control'],
                'placeholder' => 'Sélectionnez un client',
            ])
            ->add('mecanicien', EntityType::class, [
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.role = :role')
                        ->setParameter('role', 'mecanicien')
                        ->orderBy('u.nom', 'ASC');
                },
                'choice_label' => function (User $user) {
                    return $user->getNom() . ' ' . $user->getPrenom() . ' ID : ' . $user->getId();
                },
                'label' => 'Mécanicien',
                'attr' => ['class' => 'form-control'],
                'placeholder' => 'Sélectionnez un mécanicien',
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
                'attr' => ['class' => 'form-control']
            ])
            ->add('note', TextType::class, [
                'label' => 'Note',
                'attr' => ['class' => 'form-control'],
                'required' => false
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
            'data_class' => Res_mecanicien::class,
        ]);
    }
}