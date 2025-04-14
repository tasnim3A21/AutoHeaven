<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User; // make sure this is included if you're using data_class

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('username', TextType::class, [
            'label' => 'Username',
            'attr' => ['class' => 'form-control'],
            'required' => true,
        ])
        
            ->add('nom', TextType::class, [
                'label' => 'Your Name', 
                'attr' => ['class' => 'form-control']
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Your Surname', 
                'attr' => ['class' => 'form-control']
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Your Address', 
                'attr' => ['class' => 'form-control']
            ])
            ->add('profile_image', FileType::class, [
                'label' => 'Profile Picture (JPEG/PNG)',
                'required' => false,
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Image([
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'maxSize' => '2M',
                        'maxSizeMessage' => 'The file is too large. Maximum size allowed is 2MB.',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
