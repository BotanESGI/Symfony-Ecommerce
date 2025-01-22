<?php

namespace App\Controller;

use App\Service\CartService;
use App\Entity\Cart;
use App\Entity\DigitalProduct;
use App\Repository\ProductRepository;
use App\Repository\CartRepository;
use App\Entity\CartItem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart_page', methods: ['GET'])]
    public function viewCart(CartRepository $cartRepository): Response
    {
        // Vérifie si l'utilisateur est banni
        if ($this->isGranted('ROLE_BANNED')) {
            return $this->redirectToRoute('default');
        }

        // Vérifie si connecté et récupère le panier de l'utilisateur
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour supprimer un avis.');
            return $this->redirectToRoute('login');
        }
        $cart = $cartRepository->findOneBy(['user' => $user]);

        if (!$cart) {
            $cartItems = [];
        } else {
            $cartItems = $cart->getCartItems()->toArray();
        }

        $cartItemsWithType = [];
        foreach ($cartItems as $item) {
            $productType = $item->getProduct() instanceof DigitalProduct ? 'Digital' : 'Physique';
            $cartItemsWithType[] = [
                'item' => $item,
                'productType' => $productType,
            ];
        }

        return $this->render('cart/cart.html.twig', [
            'cartItems' => $cartItemsWithType,
            'cartTotal' => array_reduce($cartItems, function ($total, $item) {
                return $total + ($item->getProduct()->getPrice() * $item->getQuantity());
            }, 0)
        ]);
    }


    #[Route('/add-to-cart/{id}', name: 'add_to_cart', methods: ['POST'])]
    public function addToCart(int $id, Request $request, ProductRepository $productRepository, CartRepository $cartRepository, EntityManagerInterface $entityManager): Response
    {
        //Récupere la page par laquelle il est venue
        $referer = $request->headers->get('referer');

        if (!$referer) {
            $referer = $this->generateUrl('home_page');
        }

        //Verifie si banni
        if ($this->isGranted('ROLE_BANNED')) {
            return $this->redirectToRoute('default');
        }

        // Récupérer le produit
        $product = $productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvé.');
        }

        // Récupérer la quantité du formulaire
        $quantity = (int) $request->request->get('quantity', 1);
        if ($quantity < 1) {
            $this->addFlash('error', 'La quantité doit être au moins de 1.');
            return $this->redirect($referer);
        }

        // Verifie si connecté et récupérer le panier de l'utilisateur
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour supprimer un avis.');
            return $this->redirectToRoute('login');
        }
        $cart = $cartRepository->findOneBy(['user' => $user]);

        if (!$cart) {
            // Si le panier n'existe pas, en créer un nouveau
            $cart = new Cart();
            $cart->setUser($user);
            $entityManager->persist($cart);
        }

        // Vérifier si le produit est déjà dans le panier
        $existingCartItem = $cart->getCartItems()->filter(function ($cartItem) use ($product) {
            return $cartItem->getProduct()->getId() === $product->getId();
        })->first();

        // Si le produit est déjà dans le panier, on met à jour la quantité sinon on créer un nouvel élément de panier
        if ($existingCartItem) {
            $existingCartItem->setQuantity($existingCartItem->getQuantity() + $quantity);
        } else {
            $cartItem = new CartItem();
            $cartItem->setCart($cart)
                ->setProduct($product)
                ->setQuantity($quantity);

            $entityManager->persist($cartItem);
            $cart->addCartItem($cartItem);
        }

        $entityManager->flush();

        $this->addFlash('success', 'Produit ajouté au panier avec succès.');
        return $this->redirectToRoute("cart_page");
    }

    #[Route('/update-cart-item/{id}', name: 'update_cart_item', methods: ['POST'])]
    public function updateCartItem(int $id, Request $request, CartRepository $cartRepository, EntityManagerInterface $entityManager): Response
    {
        //Verifie si banni
        if ($this->isGranted('ROLE_BANNED')) {
            return $this->redirectToRoute('default');
        }

        //verifie si connecter
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour modifier votre panier.');
            return $this->redirectToRoute('login');
        }

        $cart = $cartRepository->findOneBy(['user' => $user]);
        if (!$cart) {
            $this->addFlash('error', 'Panier non trouvé.');
            return $this->redirectToRoute('cart_page');
        }

        $quantity = (int) $request->request->get('quantity', 1);
        if ($quantity < 1) {
            $this->addFlash('error', 'La quantité doit être au moins de 1.');
            return $this->redirectToRoute('cart_page');
        }

        // Récupérer l'élément du panier
        $cartItem = $cart->getCartItems()->filter(function ($item) use ($id) {
            return $item->getId() === $id;
        })->first();

        if ($cartItem) {
            $cartItem->setQuantity($quantity);
            $entityManager->flush();
            $this->addFlash('success', 'Quantité mise à jour avec succès.');
        } else {
            $this->addFlash('error', 'Élément non trouvé dans le panier.');
        }

        return $this->redirectToRoute('cart_page');
    }

    #[Route('/remove-cart-item/{id}', name: 'remove_cart_item', methods: ['POST'])]
    public function removeCartItem(int $id, CartRepository $cartRepository, EntityManagerInterface $entityManager): Response
    {
        //Verifie si banni
        if ($this->isGranted('ROLE_BANNED')) {
            return $this->redirectToRoute('default');
        }

        //Verifie si connecter
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour supprimer un élément de votre panier.');
            return $this->redirectToRoute('login');
        }

        $cart = $cartRepository->findOneBy(['user' => $user]);
        if (!$cart) {
            $this->addFlash('error', 'Panier non trouvé.');
            return $this->redirectToRoute('cart_page');
        }

        // Récupérer l'élément du panier
        $cartItem = $cart->getCartItems()->filter(function ($item) use ($id) {
            return $item->getId() === $id;
        })->first();

        if ($cartItem) {
            $cart->removeCartItem($cartItem);
            $entityManager->remove($cartItem);
            $entityManager->flush();
            $this->addFlash('success', 'Élément supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Élément non trouvé dans le panier.');
        }

        return $this->redirectToRoute('cart_page');
    }
}
