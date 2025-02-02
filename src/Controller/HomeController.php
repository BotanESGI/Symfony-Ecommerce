<?php
namespace App\Controller;

use App\Repository\TagRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class HomeController extends AbstractController
{
    private CartService $cartService;
    private RequestStack $requestStack;

    public function __construct(CartService $cartService, RequestStack $requestStack)
    {
        $this->cartService = $cartService;
        $this->requestStack = $requestStack;
    }

    #[Route('/tag-products/{id}', name: 'tag_products')]
    public function getProductsByTag(int $id, ProductRepository $productRepository, TagRepository $tagRepository): JsonResponse
    {
        $products = $productRepository->findAllProductByTag($id);
        $tag = $tagRepository->find($id);
        $result = [];
        foreach ($products as $product) {
            $result[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),
                'image' => $product->getImage() ? $product->getImage() : null,
                'defaultCategoryId' => $product->getDefaultCategory()->getId(),
                'tag_name' => $tag->getName(),
                'tag_color' => $tag->getColor(),
            ];
        }

        return $this->json($result);
    }

    #[Route('/home', name: 'home_page')]
    public function index(ProductRepository $productRepository, TagRepository $tagRepository, Request $request): Response
    {
        $cartItems = $this->cartService->getCartItems();
        $cartTotal = $this->cartService->getCartTotal();

        $bestRatedProducts = $productRepository->findByBestRated(10);
        $cheapestProduct = $productRepository->findByPriceASC(10);
        $mostSoldProducts = $productRepository->findMostSoldProduct(10);

        $session = $this->requestStack->getCurrentRequest()->getSession();
        $recentlyViewedIds = $session->get('recently_viewed', []);
        $recentlyViewedProducts = $productRepository->findBy(['id' => $recentlyViewedIds]);


        $tags = $tagRepository->findAll();

        return $this->render('index.html.twig', [
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
            'bestRatedProducts' => $bestRatedProducts,
            'cheapestProduct' => $cheapestProduct,
            'recentlyViewedProducts' => $recentlyViewedProducts,
            'mostSoldProducts' => $mostSoldProducts,
            'tags' => $tags,
        ]);
    }
}
