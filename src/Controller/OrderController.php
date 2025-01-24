<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Invoice;
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

    private function isAccessedFromRoutes(Request $request, array $routeNames): bool
    {
        $referer = $request->headers->get('referer');

        if (!$referer) {
            return false;
        }

        foreach ($routeNames as $routeName) {
            $routeUrl = $this->generateUrl($routeName);
            if (str_contains($referer, $routeUrl)) {
                return true;
            }
        }

        return false;
    }

    #[Route('/order/confirmation/{id}', name: 'order_confirmation', methods: ['GET'])]
    public function orderConfirmation(Request $request, int $id, EntityManagerInterface $entityManager): Response
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

        // Vérifiez si la page personne viens depuis le tunnel
        if (!$this->isAccessedFromRoutes($request, ['process_order'])) {
            return $this->redirect($referer);
        }

        $order = $entityManager->getRepository(Orders::class)->find($id);

        // Vérifier si la commande existe
        if (!$order) {
            throw $this->createNotFoundException('Commande non trouvée.');
        }

        // Vérifier que l'utilisateur connecté est bien le propriétaire de la commande
        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à accéder à cette commande.');
        }

        $orderItems = $entityManager->getRepository(OrderItem::class)->findBy(['order' => $order]);

        return $this->render('order/confirmation.html.twig', [
            'order' => $order,
            'orderItems' => $orderItems,
            'message' => 'Votre commande a été enregistrée avec succès. Merci pour votre achat !',
            'cartItems' => [],
            'cartTotal' => 0,
        ]);
    }

    #[Route('/order/tunnel-order', name: 'process_order', methods: ['GET', 'POST'])]
    public function processOrder(Request $request, EntityManagerInterface $entityManager,  InvoiceController $invoiceController): Response
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

        // Vérifiez si la page personne viens depuis le panier / ou la page du tunnel pour le POST
        if (!$this->isAccessedFromRoutes($request, ['cart_page', 'process_order'])) {
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
                        $orderItems[] = $orderItem;
                    }

                    // Supprimez le panier
                    $cart = $entityManager->getRepository(Cart::class)->findOneBy(['user' => $user]);
                    if ($cart) {
                        foreach ($cart->getCartItems() as $cartItem) {
                            $entityManager->remove($cartItem);
                        }
                        $entityManager->remove($cart);
                    }

                    // Créer la facture
                    $invoice = new Invoice();
                    $invoice->setTotalAmount($cartTotal);
                    $invoice->setUser($user);
                    $invoice->setOrder($order);

                    $order->setInvoice($invoice);

                    $entityManager->persist($invoice);
                    $entityManager->persist($order);
                    $entityManager->flush();

                    // Appel pour générer le PDF
                    $pdfResponse = $invoiceController->generateInvoicePdf($invoice, $orderItems);

                    if ($pdfResponse->getStatusCode() === 200) {
                        $pdfData = json_decode($pdfResponse->getContent(), true);
                        $invoice->setPdfPath($pdfData['path']);
                    }

                    $entityManager->persist($invoice);
                    $entityManager->flush();

                    return $this->redirectToRoute('order_confirmation', ['id' => $order->getId()]);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Error lors du payement Stripe : ' . $e->getMessage());
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
