<?php

namespace App\Form;

use App\Entity\OrderItem;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Positive;

class OrderItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
            ->add('quantity', NumberType::class, [
                'required' => true,
                'label' => 'Quantité',
                'data' => 1,
                'attr' => [
                    'class' => 'w-full p-2 border border-gray-300 rounded-lg',
                    'min' => 1,
                ],
                'constraints' => [
                    new Positive(['message' => 'La quantité doit être au moins 1.']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OrderItem::class,
        ]);
    }
}
