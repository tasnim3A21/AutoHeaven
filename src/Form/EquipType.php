<?php

namespace App\Form;

use App\Entity\Equipement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class EquipType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
              
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
               
            ])
            ->add('image', FileType::class, [
                'label' => 'Image de l\'équipement',
               // This field is not directly mapped to the entity
                'required' => true,
               
            ])
            ->add('reference', TextType::class, [
                'label' => 'Référence',
               
            ])
            ->add('marque', TextType::class, [
                'label' => 'Marque',
               
            ])
            ->add('quantite', NumberType::class, [
                'label' => 'Quantité',
                'mapped' => false, // Not part of Equipement entity
              
                'data' => 0, // Default value
            ])
            ->add('prixvente', NumberType::class, [
                'label' => 'Prix de vente',
                'mapped' => false, // Not part of Equipement entity
                
                'data' => 0.0, // Default value
            ]);
           
    }

    /*public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipement::class,
        ]);
    }*/
    public function configureOptions(OptionsResolver $resolver): void
{
    $resolver->setDefaults([
        'data_class' => Equipement::class,
        'quantite' => 0,
        'prixvente' => 0.0,
    ]);
}
}