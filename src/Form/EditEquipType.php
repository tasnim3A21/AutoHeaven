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
use Symfony\Component\Validator\Constraints\Image;

class EditEquipType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => ['class' => 'form-control'],
                'empty_data' => '',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control', 'rows' => 4],
                'empty_data' => '',
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Changer l\'image',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPEG, PNG ou GIF)',
                    ])
                ],
            ])
            ->add('reference', TextType::class, [
                'label' => 'Référence',
                'attr' => ['class' => 'form-control'],
                'empty_data' => '',
            ])
            ->add('marque', TextType::class, [
                'label' => 'Marque',
                'attr' => ['class' => 'form-control'],
                'empty_data' => '',
            ])
            ->add('quantite', NumberType::class, [
                'label' => 'Quantité en stock',
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'data' => $options['quantite'],
            ])
            ->add('prixvente', NumberType::class, [
                'label' => 'Prix de vente',
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'scale' => 2,
                'data' => $options['prixvente'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipement::class,
            'quantite' => 0,
            'prixvente' => 0.0,
        ]);
    }
}