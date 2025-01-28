<?php

namespace App\Controller;

use App\Entity\DigitalProduct;
use App\Entity\PhysicalProduct;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ApiController extends AbstractController
{
    /**
     * Liste tous les produits au format JSON structuré
     * 
     * @param ProductRepository $productRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/products', name: 'api_products', methods: ['GET'])]
    public function getProducts(ProductRepository $productRepository): JsonResponse
    {
        try {
            // Récupération des produits
            $products = $productRepository->findAll();

            // Transformation et normalisation
            $data = array_map(function (Product $product) {
                // Structure JSON uniforme
                $productData = [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'description' => $product->getDescription(),
                    'price' => $product->getPrice(),
                    'image' => $product->getImage(),
                    'defaultCategory' => $product->getDefaultCategory()->getName(),
                    'type' => $product->getProductType(),
                    'categories' => array_map(fn($category) => $category->getName(), $product->getCategories()->toArray()),
                ];

                // Ajout des caractéristiques spécifiques pour PhysicalProduct
                if ($product instanceof PhysicalProduct) {
                    $productData['features'] = $product->getCharacteristics();
                    $productData['characteristics'] = $product->getCharacteristics();
                }

                // Ajout des informations spécifiques pour DigitalProduct
                if ($product instanceof DigitalProduct) {
                    $productData['downloadLink'] = $product->getDownloadLink();
                    $productData['filesize'] = $product->getFilesize();
                    $productData['filetype'] = $product->getFiletype();
                }

                // Ajout des actions disponibles
                $productData['actions'] = [
                    'view' => "/products/{$product->getId()}",
                    'edit' => "/products/{$product->getId()}/edit",
                    'delete' => "/products/{$product->getId()}/delete",
                ];

                return $productData;
            }, $products);

            return $this->jsonResponse('success', 'Liste des produits récupérée avec succès', $data, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return $this->jsonResponse('error', 'Erreur lors de la récupération des produits', [], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Génère une réponse JSON uniforme et structurée
     * 
     * @param string $status
     * @param string $message
     * @param array|object $data
     * @param int $statusCode
     * @return JsonResponse
     */
    private function jsonResponse(string $status, string $message, $data = [], int $statusCode = JsonResponse::HTTP_OK): JsonResponse
    {
        return new JsonResponse([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }
}
