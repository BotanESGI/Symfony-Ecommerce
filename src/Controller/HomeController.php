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

        $bestRatedProducts = $productRepository->findByBestRated(10);
        $cheapestProduct = $productRepository->findByPriceASC(10);
        $mostSoldProducts = $productRepository->findMostSoldProduct(10);

        $session = $this->requestStack->getCurrentRequest()->getSession();
        $recentlyViewedIds = $session->get('recently_viewed', []);
        $recentlyViewedProducts = $productRepository->findBy(['id' => $recentlyViewedIds]);

        return $this->render('index.html.twig', [
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
            'bestRatedProducts' => $bestRatedProducts,
            'cheapestProduct' => $cheapestProduct,
            'recentlyViewedProducts' => $recentlyViewedProducts,
            'mostSoldProducts' => $mostSoldProducts,
        ]);
    }
}
