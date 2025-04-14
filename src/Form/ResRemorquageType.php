<?php

namespace App\Form;

use App\Entity\Res_remorquage;
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
            ->add('id_cr', NumberType::class, [
                'label' => 'ID Camion',
                'attr' => ['class' => 'form-control']
            ])
            ->add('id_u', NumberType::class, [
                'label' => 'ID Utilisateur',
                'attr' => ['class' => 'form-control']
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