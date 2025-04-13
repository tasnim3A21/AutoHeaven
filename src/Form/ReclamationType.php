<?php
// src/Form/ReclamationType.php
namespace App\Form;

use App\Entity\Reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Objet',
                'attr' => [
                    'class' => 'form-input',
                    'placeholder' => 'Entrez l\'objet de votre réclamation',
                ],
            ])
            ->add('contenu', TextareaType::class, [
                'label' => 'Votre réclamation',
                'attr' => [
                    'class' => 'form-textarea',
                    'rows' => 5,
                    'placeholder' => 'Décrivez votre réclamation ici',
                ],
            ])
            ->add('imageFile', FileType::class, [
                'label' => false,
                'mapped' => true,
                'required' => false,
                'attr' => [
                    'id' => 'form_imageFile',
                    'class' => 'form-file-input',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}