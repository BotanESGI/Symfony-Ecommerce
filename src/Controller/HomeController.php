<?php
namespace App\Controller;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class HomeController extends AbstractController
{
    private CartService $cartService;
    private RequestStack $requestStack;

    public function __construct(CartService $cartService, RequestStack $requestStack)
    {
        $this->cartService = $cartService;
        $this->requestStack = $requestStack;
    }

    #[Route('/home', name: 'home_page')]
    public function index(ProductRepository $productRepository): Response
    {
        $cartItems = $this->cartService->getCartItems();
        $cartTotal = $this->cartService->getCartTotal();

        // Récupération des meilleurs produits et des promotions
        $bestRatedProducts = $productRepository->findByBestRated();
        $discountedProducts = $productRepository->findByDiscounted();

        // Récupérer le dernier produit consulté depuis la session
        $session = $this->requestStack->getSession();
        $lastViewedProduct = $session->get('last_viewed_product');

        return $this->render('index.html.twig', [
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
            'bestRatedProducts' => $bestRatedProducts,
            'discountedProducts' => $discountedProducts,
            'lastViewedProduct' => $lastViewedProduct, // Ajout du dernier produit consulté
        ]);
    }
}
