<?php

namespace App\Controller;

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
    public function getProducts(ProductRepository $productRepository, SerializerInterface $serializer): JsonResponse
    {
        try {
            // Récupération des produits
            $products = $productRepository->findAll();

            // Normalisation des produits pour obtenir une structure lisible
            $data = array_map(function (Product $product) use ($serializer) {
                return $serializer->normalize($product, null, [
                    'groups' => ['product:read'],
                ]);
            }, $products);

            return $this->jsonResponse('success', 'Liste des produits récupérée avec succès', $data, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return $this->jsonResponse('error', 'Erreur lors de la récupération des produits', [], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Crée un nouveau produit
     * 
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route('/api/products', name: 'api_product_create', methods: ['POST'])]
    public function createProduct(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        try {
            $data = $request->getContent();

            // Désérialisation pour transformer le JSON en objet Product
            $product = $serializer->deserialize($data, Product::class, 'json', [
                'groups' => ['product:write'],
            ]);

            // Validation simple
            if (empty($product->getName()) || $product->getPrice() <= 0) {
                return $this->jsonResponse('error', 'Données invalides. Vérifiez le nom ou le prix.', [], JsonResponse::HTTP_BAD_REQUEST);
            }

            // Sauvegarde dans la base de données
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->jsonResponse('success', 'Produit créé avec succès', [
                'id' => $product->getId(),
                'name' => $product->getName(),
            ], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->jsonResponse('error', $e->getMessage(), [], JsonResponse::HTTP_BAD_REQUEST);
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
