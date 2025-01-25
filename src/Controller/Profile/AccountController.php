<?php


namespace App\Controller\Profile;

use App\Entity\User;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;

class AccountController extends AbstractController
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    #[Route('profile/account', name: 'account')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('ROLE_BANNED')) {
            return $this->redirectToRoute('default');
        }

        // Vérifier si l'utilisateur est déjà connecté
        if (!$this->getUser()) {
            $referer = $request->headers->get('referer');

            if (!$referer) {
                $referer = $this->generateUrl('home_page');
            }
            return $this->redirect($referer);
        }

        /** @var User $user */
        $user = $this->getUser();

        //Récupere le panier
        $cartItems = $this->cartService->getCartItems();
        $cartTotal = $this->cartService->getCartTotal();

        $form = $this->createFormBuilder($user)
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => ['class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline']
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline']
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Nouveau Mot de Passe',
                'required' => true,
                'data' => 'BotanESGI',
                'constraints' => [
                    new Assert\Length(['min' => 8]),
                    new Assert\Regex([
                        'pattern' => '/[A-Z]/',
                        'message' => 'Le mot de passe doit contenir au moins une lettre majuscule.'
                    ]),
                    new Assert\Regex([
                        'pattern' => '/[0-9]/',
                        'message' => 'Le mot de passe doit contenir au moins un chiffre.'
                    ]),
                    new Assert\Regex([
                        'pattern' => '/[\W_]/',
                        'message' => 'Le mot de passe doit contenir au moins un caractère spécial.'
                    ]),
                ],
                'attr' => ['class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline']
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Modifier',
                'attr' => ['class' => 'mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline']
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();
            if (!empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $user->setPassword($hashedPassword);
            }
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte a été mis à jour.');

            return $this->redirectToRoute('profile');
        }

        return $this->render('profile/account/account.html.twig', [
            'form' => $form->createView(),
            'email' => $user->getEmail(),
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
        ]);
    }

    #[Route('profile/account/delete', name: 'account_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si l'utilisateur est banni
        if ($this->isGranted('ROLE_BANNED')) {
            return $this->redirectToRoute('default');
        }

        $user = $this->getUser();

        // Vérifier si l'utilisateur est connecté
        if (!$user || !$this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $referer = $request->headers->get('referer');

            if (!$referer) {
                $referer = $this->generateUrl('home_page');
            }
            return $this->redirect($referer);
        }
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'Votre compte a été supprimé.');

        $request->getSession()->invalidate();

        return $this->redirectToRoute('default');
    }
}
