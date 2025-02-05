<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\DigitalProduct;
use App\Entity\PhysicalProduct;
use App\Entity\Product;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class ProductEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'required' => true,
                'attr' => [
                    'class' => 'w-full p-2 border border-gray-300 rounded-lg',
                ]
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
                'required' => true,
                'attr' => [
                    'class' => 'w-full p-2 border border-gray-300 rounded-lg',
                ]
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix (€)',
                'required' => true,
                'attr' => [
                    'class' => 'w-full p-2 border border-gray-300 rounded-lg',
                ]
            ])
            ->add('image', FileType::class, [
                'label' => 'Image du produit',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'w-full p-2 border border-gray-300 rounded-lg',
                ]
            ])
            ->add('defaultCategory', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'required' => true,
                'label' => 'Catégorie par défaut',
                'attr' => [
                    'class' => 'w-full p-2 border border-gray-300 rounded-lg',
                ],
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'label' => 'Catégories',
                'attr' => [
                    'class' => 'w-full p-2 border border-gray-300 rounded-lg',
                ],
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'label' => 'Tags',
                'attr' => [
                    'class' => 'w-full p-2 border border-gray-300 rounded-lg',
                ],
            ]);


        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $product = $event->getData();
            $form = $event->getForm();

            if ($product instanceof DigitalProduct) {
                $form->add('downloadLink', TextType::class, [
                    'required' => true,
                    'label' => 'Lien de téléchargement',
                    'attr' => [
                        'class' => 'w-full p-2 border border-gray-300 rounded-lg',
                    ]
                ]);
                $form->add('filesize', NumberType::class, [
                    'required' => false,
                    'label' => 'Taille du fichier (en Mo)',
                    'attr' => [
                        'class' => 'w-full p-2 border border-gray-300 rounded-lg',
                    ]
                ]);
                $form->add('filetype', TextType::class, [
                    'required' => false,
                    'label' => 'Type de fichier',
                    'attr' => [
                        'class' => 'w-full p-2 border border-gray-300 rounded-lg',
                    ]
                ]);
                $form->add('createdAt', DateTimeType::class, [
                    'label' => 'Date de création',
                    'required' => false,
                    'widget' => 'single_text',
                    'attr' => [
                        'class' => 'w-full p-2 border border-gray-300 rounded-lg',
                    ],
                ]);
            } elseif ($product instanceof PhysicalProduct) {
                $form->add('characteristics', TextareaType::class, [
                    'required' => false,
                    'label' => 'Caractéristiques (JSON)',
                    'attr' => [
                        'placeholder' => 'Entrez les caractéristiques au format JSON (ex : {"couleur": "rouge", "taille": "M"})',
                        'rows' => 10,
                        'style' => 'width: 100%; resize: vertical;',
                    ],
                    'data' => $product->getCharacteristics()
                        ? json_encode($product->getCharacteristics(), JSON_PRETTY_PRINT)
                        : null,
                ]);
                $form->add('createdAt', DateTimeType::class, [
                    'label' => 'Date de création',
                    'required' => false,
                    'widget' => 'single_text',
                    'attr' => [
                        'class' => 'w-full p-2 border border-gray-300 rounded-lg',
                    ],
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
