<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'product_page')]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        if ($this->isGranted('ROLE_BANNED')) {
            return $this->redirectToRoute('default');
        }

        // Récupérer les paramètres de recherche et de filtre
        $searchTerm = $request->query->get('search');

        // Récupérer toutes les produits par défaut
        $products = $productRepository->findAll();

        // Filtrage par titre
        if ($searchTerm) {
            $products = array_filter($products, function($product) use ($searchTerm) {
                return strpos($product->getTitre(), $searchTerm) !== false;
            });
        }

        return $this->render('product/product.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/product/{id}', name: 'product_detail')]
    public function detail(
        int                  $id,
        ProductRepository    $productRepository,
        ReviewRepository     $reviewRepository,
    ): Response {

        if ($this->isGranted('ROLE_BANNED')) {
            return $this->redirectToRoute('default');
        }

        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvée.');
        }

        $review = $reviewRepository->findValidReviewForProduct($product);

        return $this->render('product/product_detail.html.twig', [
            'product' => $product,
            'review' => $review,
        ]);
    }
}
