<?php
namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;


class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('description', TextType::class)
            ->add('price')
            ->add('image', FileType::class, [
                'label' => 'Image du produit',
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG, GIF).',
                    ])
                ],
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Produit physique' => 'physique',
                    'Produit digital' => 'digital',
                ],
            ])
            ->add('downloadLink', TextType::class, [
                'required' => false,
                'label' => 'Lien de téléchargement',
            ])
            ->add('filesize', TextType::class, [
                'required' => false,
                'label' => 'Taille du fichier',
            ])
            ->add('filetype', TextType::class, [
                'required' => false,
                'label' => 'Type de fichier',
            ])
            ->add('characteristics', TextType::class, [
                'required' => false,
                'label' => 'Caractéristiques',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
