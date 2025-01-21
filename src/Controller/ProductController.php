<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'product_page')]
    public function index(Request $request, ProductRepository $productRepository, CategoryRepository $categoryRepository): Response {
        if ($this->isGranted('ROLE_BANNED')) {
            return $this->redirectToRoute('default');
        }

        // Récupération des paramètres de requête
        $searchTerm = $request->query->get('search');
        $categoryId = $request->query->get('category');

        // Récupérer toutes les catégories pour le filtre
        $categories = $categoryRepository->findAll();

        // Récupérer les produits
        if ($categoryId) {
            $products = $productRepository->findByCategory($categoryId);
            $defaultCategoryProducts = $productRepository->findBy(['defaultCategory' => $categoryId]);
            $products = array_merge($products, $defaultCategoryProducts);
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
        ]);
    }


    #[Route('/product/{id}', name: 'product_detail')]
    public function detail(
        int $id, Request $request, ProductRepository $productRepository, ReviewRepository $reviewRepository, CategoryRepository $categoryRepository): Response {

        if ($this->isGranted('ROLE_BANNED')) {
            return $this->redirectToRoute('default');
        }

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

        return $this->render('product/product_detail.html.twig', [
            'product' => $product,
            'review' => $review,
            'categoryId' => $categoryId,
            'categoryName' => $categoryName,
        ]);
    }
}
