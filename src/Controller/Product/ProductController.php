<?php

namespace App\Controller\Product;

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
    public function index(Request $request, ProductRepository $productRepository, CategoryRepository $categoryRepository): Response {
        if ($this->isGranted('ROLE_BANNED')) {
            return $this->redirectToRoute('default');
        }

        //Récupere le panier
        $cartItems = $this->cartService->getCartItems();
        $cartTotal = $this->cartService->getCartTotal();

        // Récupération des paramètres de requête
        $searchTerm = $request->query->get('search');
        $categoryId = $request->query->get('category');

        // Récupérer toutes les catégories pour le filtre
        $categories = $categoryRepository->findAll();

        // Récupérer les produits
        if ($categoryId) {
            $products = $productRepository->findByCategory($categoryId);
            $defaultCategoryProducts = $productRepository->findBy(['defaultCategory' => $categoryId]);

            // Créer un tableau pour stocker les identifiants des produits déjà présents
            $existingProductIds = array_map(function($product) {
                return $product->getId();
            }, $products);

            // Ajouter les produits de la catégorie par défaut uniquement s'ils ne sont pas déjà présents pour eviter les doublons
            foreach ($defaultCategoryProducts as $defaultProduct) {
                if (!in_array($defaultProduct->getId(), $existingProductIds)) {
                    $products[] = $defaultProduct;
                }
            }
        } else {
            $products = $productRepository->findAll();
        }

        // Filtrer les produits par mot-clé
        if ($searchTerm) {
            $products = array_filter($products, function ($product) use ($searchTerm) {
                return stripos($product->getName(), $searchTerm) !== false;
            });
        }

        return $this->render('product/product.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategoryId' => $categoryId,
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
