<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressFrontType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('street', TextType::class, [
                'label' => 'Rue',
                'required' => true,
                'attr' => [
                    'class' => 'form-control border rounded-lg p-2 w-full focus:outline-none focus:ring focus:ring-blue-300',
                    'placeholder' => 'Entrez votre rue',
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => true,
                'attr' => [
                    'class' => 'form-control border rounded-lg p-2 w-full focus:outline-none focus:ring focus:ring-blue-300',
                    'placeholder' => 'Entrez votre ville',
                ],
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code postal',
                'required' => true,
                'attr' => [
                    'class' => 'form-control border rounded-lg p-2 w-full focus:outline-none focus:ring focus:ring-blue-300',
                    'placeholder' => 'Entrez votre code postal',
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
