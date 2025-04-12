<?php

namespace App\Form;

use App\Entity\Messagerie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ReclamationReponseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ titre (caché, pour préserver la valeur)
            ->add('titre', HiddenType::class, [
                'label' => 'Titre de la réclamation',
                'required' => false,
                'mapped' => false,
                'data' => $options['reclamation_titre'] ?? 'Non défini',
            ])
            // Champ contenu (caché, pour préserver la valeur)
            ->add('contenu', HiddenType::class, [
                'label' => 'Objet de la réclamation',
                'required' => false,
                'mapped' => false,
                'data' => $options['reclamation_contenu'] ?? 'Non défini',
            ])
            // Champ message (modifiable)
            ->add('message', TextareaType::class, [
                'label' => 'Votre réponse',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le message ne peut pas être vide.']),
                    new Assert\Length([
                        'min' => 10,
                        'minMessage' => 'Le message doit contenir au moins 10 caractères.',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-textarea',
                    'rows' => '3',
                ],
            ])
            // Bouton de soumission
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'btn-submit',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Messagerie::class,
            'csrf_protection' => true,
            'reclamation_titre' => null,
            'reclamation_contenu' => null,
        ]);

        $resolver->setAllowedTypes('reclamation_titre', ['string', 'null']);
        $resolver->setAllowedTypes('reclamation_contenu', ['string', 'null']);
    }
}