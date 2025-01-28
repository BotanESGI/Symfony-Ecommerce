<?php

namespace App\Controller;

use App\Entity\DigitalProduct;
use App\Entity\PhysicalProduct;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ApiController extends AbstractController
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

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
                    'type' => $product->getProductType(),
                    'categories' => array_map(fn($category) => $category->getName(), $product->getCategories()->toArray()),
                    'defaultCategory' => $product->getDefaultCategory()->getName(),
                ];

                // Ajout des caractéristiques spécifiques pour PhysicalProduct
                if ($product instanceof PhysicalProduct) {
                    $productData['characteristics'] = $product->getCharacteristics();
                }

                // Ajout des informations spécifiques pour DigitalProduct
                if ($product instanceof DigitalProduct) {
                    $productData['downloadLink'] = $product->getDownloadLink();
                    $productData['filesize'] = $product->getFilesize();
                    $productData['filetype'] = $product->getFiletype();
                }

                return $productData;
            }, $products);

            return $this->jsonResponse('success', 'Catalogue produit récupérée avec succès', $data, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return $this->jsonResponse('error', 'Erreur lors de la récupération du catalogue produit', [], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Récupère un produit spécifique par son ID
     *
     * @param ProductRepository $productRepository
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/api/products/{id}', name: 'api_product', methods: ['GET'])]
    public function getProduct(ProductRepository $productRepository, int $id): JsonResponse
    {
        try {
            // Verif si le produit existe
            $product = $productRepository->find($id);
            if (!$product)
            {
                return $this->jsonResponse('error', 'Produit non trouvé', [], JsonResponse::HTTP_NOT_FOUND);
            }

            $productData =
            [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),
                'image' => $product->getImage(),
                'type' => $product->getProductType(),
                'categories' => array_map(fn($category) => $category->getName(), $product->getCategories()->toArray()),
                'defaultCategory' => $product->getDefaultCategory()->getName(),
            ];

            if ($product instanceof PhysicalProduct)
            {
                $productData['characteristics'] = $product->getCharacteristics();
            }

            if ($product instanceof DigitalProduct)
            {
                $productData['downloadLink'] = $product->getDownloadLink();
                $productData['filesize'] = $product->getFilesize();
                $productData['filetype'] = $product->getFiletype();
            }

            return $this->jsonResponse('success', 'Produit récupéré avec succès', $productData, JsonResponse::HTTP_OK);
        }
        catch (\Exception $e)
        {
            return $this->jsonResponse('error', 'Erreur lors de la récupération du produit', [], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api', name: 'api_page')]
    public function get_API(ProductRepository $productRepository): Response
    {
        if ($this->isGranted('ROLE_BANNED')) {
            return $this->redirectToRoute('default');
        }

        $cartItems = $this->cartService->getCartItems();
        $cartTotal = $this->cartService->getCartTotal();

        return $this->render('/api/api.html.twig', [
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
        ]);
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
