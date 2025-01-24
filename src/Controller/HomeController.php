<?php
namespace App\Controller;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;


class HomeController extends AbstractController
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    #[Route('/home', name: 'home_page')]
    public function index(ProductRepository $productRepository): Response
    {
        $cartItems = $this->cartService->getCartItems();
        $cartTotal = $this->cartService->getCartTotal();

        // Utilisation des méthodes modifiées dans ProductRepository
        $bestRatedProducts = $productRepository->findByBestRated(); // Méthode pour les mieux notés
        $discountedProducts = $productRepository->findByDiscounted(); // Méthode pour les produits en soldes

        return $this->render('index.html.twig', [
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
            'bestRatedProducts' => $bestRatedProducts,
            'discountedProducts' => $discountedProducts,

        ]);
    }
}