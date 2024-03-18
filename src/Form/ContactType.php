<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [

                'label'=> 'Prénom',

                'attr' => ['placeholder' => 'Votre prénom'],

                // si on veut appliquer des classes/du style sur nos champ on le fait ici avec 'attr' => ['class' => 'abcd'],
                // 'required' => false,
                // 'empty_data' => 'John Doe', // mettra par défaut "John Doe" dans la BDD si je soumets le formulaire mais que mon utilisateur n'a pas rempli le champ
                'row_attr' => ['class' => 'col-md-6', 'id' => '...'],
            ])

            ->add('name', TextType::class, [
                'label'=> 'Nom',
                'attr' => ['placeholder' => 'Votre nom'],
                'row_attr' => ['class' => 'col-md-6', 'id' => '...'],
            ])

            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['placeholder' => 'Votre email'],
                'row_attr' => ['class' => 'col-md-6', 'id' => '...'],
            ])

            ->add('object', ChoiceType::class, [
                
                'label' => 'Sélectionnez un motif de contact',

                'choices' => [ // pour que mon input devienne un Select
                    'Veuillez choisir une valeur' => NULL,
                    'Je suis devenir formateur' => 'Je souhaite devenir formateur',
                    'Je souhaite devnir développeur' => 'Je souhaite devenir développeur',
                    'Je souhaite vous contacter' => 'Je souhaite vous contacter',
                ],
                'choice_attr' => [
                    'Veuillez choisir une valeur' => ['disabled' => true] // pour qu'on ne puisse pas sélectionner cette valeur qui sert de placeholder
                ],
            ])

            ->add('message', TextareaType::class, [
                'label' => 'Votre message',
                'attr' => ['placeholder' => 'Votre message'],
                // 'mapped' => false // utilisé si j'ai un champ dans mon formulaire qui ne correspond à aucune colonne de ma classe/entité Contact. 
                // Symfony fera pas le lien entre ce champ et Contact pour éviter une erreur
            ])

            // ->add('date', DateType::class, [
            // ])

            ->add('save', SubmitType::class, [
                'label' => 'Nous contacter',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
