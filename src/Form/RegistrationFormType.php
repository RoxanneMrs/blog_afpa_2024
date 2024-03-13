<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {

        $builder
            ->add('first_name', TextType::class, [
                'label' => "Prénom",
                'attr' => ['placeholder' => 'Votre prénom'],
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Votre prénom doit être renseigné']),
                    new Length(['min' => 2, 'minMessage' => 'Votre prénom doit faire au minimum 2 caractères'])
                ],
            ])

            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Votre nom'],
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Votre nom doit être renseigné']),
                    new Length(['min' => 2, 'minMessage' => 'Votre nom doit faire au minimum 2 caractères' ])],
            ])

            ->add('phone_number', TelType::class, [
                'label' => "Numéro de téléphone",
                'attr' => ['placeholder' => 'Votre numéro de téléphone'],
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez ajouter un numéro de téléphone']),
                    new Length(['min' => 10, 'minMessage' => 'Votre numéro de téléphone doit être composé de 10 caractères',
                                'max' => 10, 'maxMessage' => 'Votre numéro de téléphone doit être composé de 10 caractères'])
                ],
            ])

            ->add('email', EmailType::class, [
                'label' => "Email",
                'attr' => ['placeholder' => 'Votre email'],
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Votre email doit être renseigné']),
                    new Email(['message' => 'Veuillez renseigner un email valide!']),
                ],
            ])

            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'attr' => ['placeholder' => 'Votre adresse'],
                'required' => true,
                'constraints' => [new NotBlank(['message' => 'Votre adresse doit être renseignée']),],
            ])

            ->add('code_postal', TextType::class, [
                'label' => 'Code postal',
                'attr' => ['placeholder' => 'Votre code postal'],
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Votre code postal doit être renseigné']),
                    new Length(['min' => 5, 'minMessage' => 'Votre code postal doit faire 5 caractères',
                                'max' => 5, 'maxMessage' => 'Votre code postal doit faire 5 caractères'])],                                 
            ])

            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Veuillez accepter les conditions.',
                    ]),
                ],
            ])

            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => 'Mot de passe',
                'required' => true,
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password', 'placeholder' => 'Votre mot de passe'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])

            ->add('newPlainPassword', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'first_options' => [
                    'constraints' => [
                        new NotBlank(['message' => 'Entrez un Mot de Passe']),
                        new Length(['min' => 6, 'minMessage' => 'Votre Mot de Passe doit faire au moins {{ limit }} caractères']),
                    ],
                    'label' => 'Nouveau Mot de Passe',
                ],
                'second_options' => ['label' => 'Répétez le nouveau mot de passe'],
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
