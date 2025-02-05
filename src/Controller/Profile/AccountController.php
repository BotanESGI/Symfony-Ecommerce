<?php


namespace App\Controller\Profile;

use App\Entity\Invoice;
use App\Entity\Orders;
use App\Entity\User;
use App\Form\UserFrontType;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountController extends AbstractController
{
    private CartService $cartService;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(CartService $cartService, UserPasswordHasherInterface $passwordHasher)
    {
        $this->cartService = $cartService;
        $this->passwordHasher = $passwordHasher;
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

        $form = $this->createForm(UserFrontType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();
            if (!empty($password)) {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
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
    public function delete(Request $request, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage): Response
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

        $orders = $entityManager->getRepository(Orders::class)->findBy(['user' => $user]);
        $invoices = $entityManager->getRepository(Invoice::class)->findBy(['user' => $user]);
        if ($invoices || $orders) {
            foreach ($invoices as $invoice) {
                $entityManager->getConnection()->executeStatement(
                    'UPDATE "invoice" SET "order_id" = NULL WHERE "id" = :id',
                    ['id' => $invoice->getId()]
                );
            }

            foreach ($orders as $order) {
                $entityManager->getConnection()->executeStatement(
                    'UPDATE "orders" SET "invoice_id" = NULL WHERE "id" = :id',
                    ['id' => $order->getId()]
                );
            }

            foreach ($orders as $order) {
                $entityManager->getConnection()->executeStatement(
                    'DELETE FROM "orders" WHERE "id" = :id',
                    ['id' => $order->getId()]
                );
            }

            foreach ($invoices as $invoice) {
                $entityManager->getConnection()->executeStatement(
                    'DELETE FROM "invoice" WHERE "id" = :id',
                    ['id' => $invoice->getId()]
                );
            }
        }

        // Maintenant, supprimer l'utilisateur
        $entityManager->remove($user);
        $entityManager->flush();

        //fix cart service erreor
        $tokenStorage->setToken(null);
        $request->getSession()->invalidate();

        $this->addFlash('success', 'Votre compte a été supprimé.');

        $request->getSession()->invalidate();

        return $this->redirectToRoute('default');
    }
}
