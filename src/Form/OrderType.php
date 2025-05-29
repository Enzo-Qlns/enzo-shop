<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Address;
use App\Entity\Carrier;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Order;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('delivery', EntityType::class, [
                'label' => 'Choisir votre adresse de livraison',
                'required' => true,
                'class' => Address::class,
                'choice_label' => 'fullAddress',
                'expanded' => true,
                'multiple' => false,
                'choices' => $options['addresses'],
                'placeholder' => 'Choisir une adresse',
                'label_html' => true,
            ])
            ->add('carriers', EntityType::class, [
                'label' => 'Choisir votre transporteur',
                'required' => true,
                'class' => Carrier::class,
                'expanded' => true,
                'multiple' => false,
                'placeholder' => 'Choisir un transporteur',
                'label_html' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn btn-success',
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'addresses'=>null,
        ]);
    }
}
