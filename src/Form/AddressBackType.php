<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class AddressBackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('street', TextType::class, [
                'label' => 'Rue',
                'required' => true,
                'attr' => [
                    'class' => 'w-full p-2 border border-gray-300 rounded-lg',
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => true,
                'attr' => [
                    'class' => 'w-full p-2 border border-gray-300 rounded-lg',
                ]
            ])
            ->add('postalCode', NumberType::class, [
                'label' => 'Code Postal',
                'required' => true,
                'attr' => [
                    'class' => 'w-full p-2 border border-gray-300 rounded-lg',
                ]
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'label' => 'Utilisateur',
                'choice_label' => function (User $user) {
                    return $user->getName() . ' ' . $user->getLastname();
                },
                'attr' => [
                    'class' => 'w-full p-2 border border-gray-300 rounded-lg',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "L'utilisateur ne doit pas être vide.",
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
