<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class PasswordUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('actualPassword', PasswordType::class, [
                'label' => 'Mot de passe actuel',
                'attr' => [
                    'placeholder' => 'Mot de passe actuel',
                ],
                'mapped' => false,
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                    new Length(['min' => 6]),
                ],
                'first_options' => ['label' => 'Choisir un mot de passe',
                    'hash_property_path' => 'password',
                    'attr' => [
                        'placeholder' => 'Saisir votre mot de passe',
                    ]],

                'second_options' => ['label' => 'Confirmer votre mot de passe', 'attr' => [
                    'placeholder' => 'Connfirmer votre mot de passe',
                ]],
                'mapped' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'MAJ le mot de passe',
                'attr' => [
                    'class' => 'btn btn-primary',
                ]
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                //recuperation de notre formulaire
                $form = $event->getForm();

                //recuperer user avec toutes ces infos (y compris le pwd)
                $user = $form->getConfig()->getOptions()['data'];

                //on recupere passwordHasher du controller
                $passwordHasher = $form->getConfig()->getOptions()['password_hasher'];

                //on compare avec les 2 mdp avec le passhashé
                $isValid = $passwordHasher->isPasswordValid(
                    $user,
                    $form->get('actualPassword')->getData()
                );

                // si c'est diferent on envoi une erreur
                if (!$isValid) {
                    $form->get('actualPassword')->addError(new FormError("Mot de passe incorrect"));
                }

                // Todo redirige vers la page du compte
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'password_hasher' => null,
        ]);
    }
}