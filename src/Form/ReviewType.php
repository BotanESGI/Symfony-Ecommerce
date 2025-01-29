<?php

namespace App\Form;

use App\Entity\Review;
use App\Entity\Product;
use App\Entity\User;
use App\Enum\ReviewStatusEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', EntityType::class, [
                'required' => true,
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getName() . ' ' . $user->getLastname();
                },
                'label' => 'Utilisateur',
                'placeholder' => 'Sélectionnez un utilisateur',
                'attr' => [
                    'class' => 'w-full p-2 border border-gray-300 rounded-lg',
                ],
            ])
            ->add('product', EntityType::class, [
                'required' => true,
                'class' => Product::class,
                'choice_label' => 'name',
                'label' => 'Produit',
                'placeholder' => 'Sélectionnez un produit',
                'attr' => [
                    'class' => 'w-full p-2 border border-gray-300 rounded-lg',
                ],
            ])
            ->add('content', TextareaType::class, [
                'required' => true,
                'label' => 'Contenu',
                'attr' => [
                    'rows' => 5,
                    'class' => 'w-full p-2 border border-gray-300 rounded-lg',
                ],
            ])
            ->add('rating', IntegerType::class, [
                'required' => true,
                'label' => 'Note',
                'attr' => [
                    'min' => 0,
                    'max' => 5,
                    'class' => 'w-full p-2 border border-gray-300 rounded-lg',
                ],
            ])
            ->add('status', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    'Pending' => ReviewStatusEnum::PENDING,
                    'Validated' => ReviewStatusEnum::VALIDATED,
                    'Rejected' => ReviewStatusEnum::REJECTED,
                ],
                'expanded' => true,
                'multiple' => false,
                'attr' => [
                    'class' => 'flex space-x-4 mt-1',
                ],
            ])
            ->add('datePublication', DateTimeType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Date de publication',
                'attr' => [
                    'class' => 'w-full p-2 border border-gray-300 rounded-lg',
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
