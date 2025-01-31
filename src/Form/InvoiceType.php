<?php

namespace App\Form;

use App\Entity\Invoice;
use App\Entity\Orders;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
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
            ])
            ->add('totalAmount', TextType::class, [
                'required' => true,
                'label' => 'Montant Total',
            ])
            ->add('order', EntityType::class, [
                'class' => Orders::class,
                'choice_label' => 'id',
                'required' => true,
                'label' => 'Commande n°',
            ])
            ->add('pdfPath', TextType::class, [
                'required' => false,
                'label' => 'Chemin du fichier PDF',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}
