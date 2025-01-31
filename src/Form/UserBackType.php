<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Regex;

class UserBackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
            ])
            ->add('lastname', TextType::class, [
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'required' => true,
            ])
            ->add('password', PasswordType::class, [
                'label' => $options['is_create']
                    ? 'Nouveau mot de passe'
                    : 'Nouveau mot de passe (laisser vide pour ne pas changer)',
                'required' => $options['is_create'],
                'mapped' => false,
                'attr' => [
                    'class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline'
                ],
                'constraints' => $options['is_create'] ? [
                    new NotBlank(['message' => 'Le mot de passe ne peut pas être vide.']),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                    ]),
                    new Regex([
                        'pattern' => '/[A-Z]/',
                        'message' => 'Le mot de passe doit contenir au moins une lettre majuscule.',
                    ]),
                    new Regex([
                        'pattern' => '/[0-9]/',
                        'message' => 'Le mot de passe doit contenir au moins un chiffre.',
                    ]),
                    new Regex([
                        'pattern' => '/[\W_]/',
                        'message' => 'Le mot de passe doit contenir au moins un caractère spécial.',
                    ]),
                ] : [],
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
                'required' => true,
            ])
            ->add('resetToken', TextType::class, [
                'required' => false,
            ])
            ->add('confirmationToken', TextType::class, [
                'required' => false,
            ])
            ->add('isVerified', ChoiceType::class, [
                'label' => 'Vérifié',
                'choices' => [
                    'Non' => 0,
                    'Oui' => 1,
                ],
                'multiple' => false,
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_create' => true,
        ]);
    }
}
