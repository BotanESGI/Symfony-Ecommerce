<?php

namespace App\Controller\Profile;

use App\Entity\Address;
use App\Entity\Orders;
use App\Form\AddressFrontType;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderHistoryController extends AbstractController
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    #[Route('profile/order_history', name: 'order_history')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si l'utilisateur est banni
        if ($this->isGranted('ROLE_BANNED')) {
            return $this->redirectToRoute('default');
        }

        $user = $this->getUser();

        // Vérifier si l'utilisateur est déjà connecté
        if (!$user) {
            $referer = $request->headers->get('referer');

            if (!$referer) {
                $referer = $this->generateUrl('home_page');
            }
            return $this->redirect($referer);
        }

        //Récupere le panier
        $cartItems = $this->cartService->getCartItems();
        $cartTotal = $this->cartService->getCartTotal();

        $orders = $entityManager->getRepository(Orders::class)->findBy(['user' => $user]);

        return $this->render('profile/order_history/order_history.html.twig', [
            'orders' => $orders,
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
        ]);
    }

    #[Route('profile/order_history_detail/{id}', name: 'order_details')]
    public function details(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si l'utilisateur est banni
        if ($this->isGranted('ROLE_BANNED')) {
            return $this->redirectToRoute('default');
        }

        // Vérifie si l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            $referer = $request->headers->get('referer');

            if (!$referer) {
                $referer = $this->generateUrl('home_page');
            }
            return $this->redirect($referer);
        }

        //Récupere le panier
        $cartItems = $this->cartService->getCartItems();
        $cartTotal = $this->cartService->getCartTotal();

        $order = $entityManager->getRepository(Orders::class)->findOneBy(['id' => $id, 'user' => $user]);

        if (!$order) {
            throw $this->createNotFoundException('Commande non trouvée.');
        }

        return $this->render('profile/order_history/order_history_detail.html.twig', [
            'order' => $order,
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
        ]);
    }
}
