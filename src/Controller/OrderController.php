<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\OrderItem;
use App\Entity\Orders;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class OrderController extends AbstractController
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    private function isAccessedFromCartOrTunnel(Request $request): bool
    {
        $referer = $request->headers->get('referer');
        $cartUrl = $this->generateUrl('cart_page');
        $tunnelOrderUrl = $this->generateUrl('process_order');

        return $referer && (str_contains($referer, $cartUrl) || str_contains($referer, $tunnelOrderUrl));
    }


    #[Route('/order/tunnel-order', name: 'process_order', methods: ['GET', 'POST'])]
    public function processOrder(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si l'utilisateur est banni
        if ($this->isGranted('ROLE_BANNED')) {
            return $this->redirectToRoute('default');
        }

        $referer = $request->headers->get('referer') ?: $this->generateUrl('home_page');

        // Vérifier si l'utilisateur n'est connecté
        if (!$this->getUser()) {
            return $this->redirect($referer);
        }

        // Vérifiez si la page personne viens depuis le panier
        if (!$this->isAccessedFromCartOrTunnel($request)) {
            return $this->redirect($referer);
        }

        $user = $this->getUser();
        // Récupère le panier
        $cartItems = $this->cartService->getCartItems();
        $cartTotal = $this->cartService->getCartTotal();

        // Vérifie si l'utilisateur a des adresses
        $addresses = $user->getAddresses();
        if ($addresses->isEmpty()) {
            $this->addFlash('error', 'Ajoutez une adresse avant de continuer.');
            return $this->redirectToRoute('create_address');
        }

        if ($request->isMethod('POST')) {
            $selectedAddressId = $request->request->get('address');
            $selectedAddress = array_filter($addresses->toArray(), fn($addr) => $addr->getId() == $selectedAddressId);

            if ($selectedAddress) {
                $request->getSession()->set('selected_address', $selectedAddressId);

                Stripe::setApiKey($this->getParameter('stripe_secret_key'));

                try {
                    $paymentIntent = PaymentIntent::create([
                        'amount' => $cartTotal * 100,
                        'currency' => 'eur',
                    ]);

                    // Créez une nouvelle commande
                    $order = new Orders();
                    $order->setUser($user)
                        ->setTotal($cartTotal)
                        ->setDate(new \DateTimeImmutable());

                    $entityManager->persist($order);

                    // Ajoutez les articles de commande
                    foreach ($cartItems as $cartItem) {
                        $orderItem = new OrderItem();
                        $orderItem->setProduct($cartItem->getProduct())
                            ->setQuantity($cartItem->getQuantity())
                            ->setOrder($order)
                            ->setPrice($cartItem->getProduct()->getPrice());
                        $entityManager->persist($orderItem);
                    }

                    // Supprimez le panier
                    $cart = $entityManager->getRepository(Cart::class)->findOneBy(['user' => $user]);
                    if ($cart) {
                        foreach ($cart->getCartItems() as $cartItem) {
                            $entityManager->remove($cartItem);
                        }
                        $entityManager->remove($cart);
                    }

                    $entityManager->flush();

                    return $this->redirectToRoute('order_confirmation');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Error creating payment intent: ' . $e->getMessage());
                }
            } else {
                $this->addFlash('error', 'Adresse sélectionnée invalide.');
            }
        }

        return $this->render('order/order.html.twig', [
            'addresses' => $addresses,
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
        ]);
    }

}
