<?php

namespace App\Controller\Product;

use App\Entity\DigitalProduct;
use App\Entity\PhysicalProduct;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\ReviewRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    #[Route('/product', name: 'product_page')]
    public function index(Request $request, ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {
        if ($this->isGranted('ROLE_BANNED')) {
            return $this->redirectToRoute('default');
        }

        $cartItems = $this->cartService->getCartItems();
        $cartTotal = $this->cartService->getCartTotal();

        $searchTerm = $request->query->get('search');
        $categoryId = $request->query->get('category');
        $type = $request->query->get('type');
        $minPrice = $request->query->get('min_price');
        $maxPrice = $request->query->get('max_price');
        $priceSort = $request->query->get('price_sort');
        $dateSort = $request->query->get('date_sort');

        $categories = $categoryRepository->findAll();
        $products = $productRepository->findAll();

        if ($categoryId) {
            $products = $productRepository->findByCategory($categoryId);
            $defaultCategoryProducts = $productRepository->findBy(['defaultCategory' => $categoryId]);

            $existingProductIds = array_map(function ($product) {
                return $product->getId();
            }, $products);

            foreach ($defaultCategoryProducts as $defaultProduct) {
                if (!in_array($defaultProduct->getId(), $existingProductIds)) {
                    $products[] = $defaultProduct;
                }
            }
        }

        $selectedCategoryName = null;
        if ($categoryId) {
            $selectedCategory = $categoryRepository->find($categoryId);
            if ($selectedCategory) {
                $selectedCategoryName = $selectedCategory->getName();
            }
        }


        if ($type) {
            $products = array_filter($products, function ($product) use ($type) {
                if ($type === 'digital') {
                    return $product instanceof DigitalProduct;
                } elseif ($type === 'physical') {
                    return $product instanceof PhysicalProduct;
                }
                return true;
            });
        }

        if ($searchTerm) {
            $products = array_filter($products, function ($product) use ($searchTerm) {
                return stripos($product->getName(), $searchTerm) !== false;
            });
        }

        if ($minPrice && is_numeric($minPrice)) {
            $products = array_filter($products, function ($product) use ($minPrice) {
                return $product->getPrice() >= $minPrice;
            });
        }

        if ($maxPrice && is_numeric($maxPrice)) {
            $products = array_filter($products, function ($product) use ($maxPrice) {
                return $product->getPrice() <= $maxPrice;
            });
        }

        if ($priceSort) {
            if ($priceSort === 'asc') {
                usort($products, fn($a, $b) => $a->getPrice() <=> $b->getPrice());
            } elseif ($priceSort === 'desc') {
                usort($products, fn($a, $b) => $b->getPrice() <=> $a->getPrice());
            }
        }

        if ($dateSort) {
            if ($dateSort === 'asc') {
                usort($products, fn($a, $b) => $a->getCreatedAt() <=> $b->getCreatedAt());
            } elseif ($dateSort === 'desc') {
                usort($products, fn($a, $b) => $b->getCreatedAt() <=> $a->getCreatedAt());
            }
        }

        return $this->render('product/product.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategoryId' => $categoryId,
            'selectedCategoryName' => $selectedCategoryName,
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
        ]);
    }


    #[Route('/product/{id}', name: 'product_detail')]
    public function detail(
        int $id, Request $request, ProductRepository $productRepository, ReviewRepository $reviewRepository, CategoryRepository $categoryRepository): Response {

        if ($this->isGranted('ROLE_BANNED')) {
            return $this->redirectToRoute('default');
        }

        //Récupere le panier
        $cartItems = $this->cartService->getCartItems();
        $cartTotal = $this->cartService->getCartTotal();

        // Récupérer le produit par ID
        $product = $productRepository->find($id);

        // Vérifier si le produit existe
        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvée.');
        }

        // Récupérer les avis pour le produit
        $review = $reviewRepository->findValidReviewsForProduct($product);

        // Récupérer l'identifiant de la catégorie depuis la requête
        $categoryId = $request->query->get('category', 0);
        $category = $categoryRepository->find($categoryId);
        $categoryName = $category ? $category->getName() : 'Category nom par defaut';

        // Vérifier que le produit appartient à la catégorie spécifiée
        if (!$product->getCategories()->exists(fn($key, $category) => $category->getId() === (int)$categoryId) &&
            $product->getDefaultCategory()->getId() !== (int)$categoryId) {
            throw $this->createNotFoundException('Catégorie incorrect.');
        }

        $tags = $product->getTags();
        $session = $request->getSession();
        $recentlyViewedIds = $session->get('recently_viewed', []);
        if (!in_array($id, $recentlyViewedIds)) {
            if (count($recentlyViewedIds) >= 10) {
                array_shift($recentlyViewedIds);
            }
            $recentlyViewedIds[] = $id;
            $session->set('recently_viewed', $recentlyViewedIds);
        }

        return $this->render('product/product_detail.html.twig', [
            'product' => $product,
            'review' => $review,
            'categoryId' => $categoryId,
            'categoryName' => $categoryName,
            'tags' => $tags,
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
        ]);
    }
}
