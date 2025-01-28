<?php

// src/Form/TagType.php

namespace App\Form;

use App\Entity\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'border border-gray-300 px-4 py-2 rounded w-full',
                    'placeholder' => 'Nom du tag',
                ],
                'required' => true,
                'label' => 'Nom',
            ])
            ->add('color', ColorType::class, [
                'attr' => [
                    'class' => 'w-full',
                ],
                'required' => true,
                'label' => 'Couleur',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tag::class,
        ]);
    }
}
