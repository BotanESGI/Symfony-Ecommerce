<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Form\FormInterface;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Nouveau mot de passe',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 8]),
                    new Regex([
                        'pattern' => '/[A-Z]/',
                        'message' => 'Le mot de passe doit contenir au moins une lettre majuscule.',
                    ]),
                    new Regex([
                        'pattern' => '/[a-z]/',
                        'message' => 'Le mot de passe doit contenir au moins une lettre minuscule.',
                    ]),
                    new Regex([
                        'pattern' => '/[0-9]/',
                        'message' => 'Le mot de passe doit contenir au moins un chiffre.',
                    ]),
                    new Regex([
                        'pattern' => '/[\W_]/',
                        'message' => 'Le mot de passe doit contenir au moins un caractère spécial.',
                    ]),
                ],
                'attr' => [
                    'class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline',
                ],
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'Confirmer le mot de passe',
                'constraints' => [
                    new NotBlank(),
                ],
                'attr' => [
                    'class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline',
                ],
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'Confirmer le mot de passe',
                'constraints' => [
                    new NotBlank(),
                    new Callback([$this, 'validatePasswords']),
                ],
                'attr' => [
                    'class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline',
                ],
            ]);
    }

    public function validatePasswords($confirmPassword, ExecutionContextInterface $context)
    {
        $form = $context->getRoot();
        $plainPassword = $form->get('plainPassword')->getData();

        if ($confirmPassword !== $plainPassword) {
            $context->buildViolation('Les mots de passe doivent correspondre.')
                ->atPath('confirmPassword')
                ->addViolation();
        }
    }
}

