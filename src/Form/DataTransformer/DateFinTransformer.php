<?php
namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class DateFinTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        return $value; // You can just return the value if no transformation is needed
    }

    public function reverseTransform($value)
    {
        $form = $value->getForm()->getParent();
        $dateDebut = $form->getData()->getDateDebut();

        if ($value < $dateDebut) {
            // You can throw an exception if the validation fails
            throw new TransformationFailedException('La date de fin ne peut pas être inférieure à la date de début.');
        }

        return $value;
    }
}
