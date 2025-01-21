<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un mot de passe.',
                    ]),
                ],
                'attr' => ['autocomplete' => 'new-password'], // Assure que le champ est traité comme un nouveau mot de passe.
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôles',
                'choices' => [
                    'Administrateur' => 'ROLE_ADMIN',
                    'Utilisateur' => 'ROLE_USER',
                    'Banni' => 'ROLE_BANNED',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('name')
            ->add('lastname')
            ->add('resetToken')
            ->add('confirmationToken')
            ->add('isVerified')
            ->add('isVerified', ChoiceType::class, [
                'choices' => [
                    'Oui' => 1,
                    'Non' => 0,
                ],
                'multiple' => false,
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
