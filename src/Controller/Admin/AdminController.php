<?php

namespace App\Controller\Admin;

use App\Controller\IsGranted;
use App\Entity\Category;
use App\Entity\DigitalProduct;
use App\Entity\PhysicalProduct;
use App\Entity\Product;
use App\Entity\Review;
use App\Form\ProductCreateType;
use App\Form\ProductEditType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[IsGranted('ROLE_ADMIN')]
final class AdminController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin', name: 'admin_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/admin.html.twig', [
        ]);
    }

    ////////////////////////////////////////////////
    ///////////////////Produits/////////////////////
    ////////////////////////////////////////////////
    #[Route('/admin/products', name: 'admin_products', methods: ['GET'])]
    public function CRUDAdminProducts(): Response
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll();
        return $this->render('admin/product/products.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/admin/products/create', name: 'admin_products_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $productType = $request->query->get('type');
        $defaultCategoryId = 1;
        $defaultCategory = $this->entityManager->getRepository(Category::class)->find($defaultCategoryId);

        if (!$defaultCategory) {
            throw $this->createNotFoundException('Category on trouvée');
        }

        if ($productType === 'digital') {
            $product = new DigitalProduct($defaultCategory);
        } else {
            $product = new PhysicalProduct($defaultCategory);
        }

        $form = $this->createForm(ProductCreateType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            if ($file) {
                $filename = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('images_directory'), $filename);
                $product->setImage($filename);
            }

            if ($product instanceof DigitalProduct) {
                $product->setDownloadLink($form->get('downloadLink')->getData());
                $product->setFilesize($form->get('filesize')->getData());
                $product->setFiletype($form->get('filetype')->getData());
            } elseif ($product instanceof PhysicalProduct) {
                $characteristicsJson = $form->get('characteristics')->getData();
                if ($characteristicsJson) {
                    $product->setCharacteristics(json_decode($characteristicsJson, true));
                } else {
                    $product->setCharacteristics([]);
                }
            }

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            $this->addFlash('success', 'Produit créé avec succès.');
            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/product/products_create.html.twig', [
            'form' => $form->createView(),
            'productType' => $productType,
        ]);
    }

    #[Route('/admin/products/edit/{id}', name: 'admin_products_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        $product = $this->entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            $this->addFlash('error', 'Le produit demandé n\'existe pas.');
            return $this->redirectToRoute('admin_products');
        }

        $form = $this->createForm(ProductEditType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            if ($file) {
                $filename = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('images_directory'), $filename);
                $product->setImage($filename);
            }

            $product->setDefaultCategory($form->get('defaultCategory')->getData());

            $categories = $form->get('categories')->getData();
            foreach ($categories as $category) {
                $product->addCategory($category);
            }

            if ($product instanceof DigitalProduct) {
                $product->setDownloadLink($form->get('downloadLink')->getData());
                $product->setFilesize($form->get('filesize')->getData());
                $product->setFiletype($form->get('filetype')->getData());
            } elseif ($product instanceof PhysicalProduct) {
                $characteristicsJson = $form->get('characteristics')->getData();
                if ($characteristicsJson) {
                    $product->setCharacteristics(json_decode($characteristicsJson, true));
                } else {
                    $product->setCharacteristics([]);
                }
            }

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            $this->addFlash('success', 'Produit modifié avec succès.');
            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/product/products_edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }


    #[Route('/admin/products/delete/{id}', name: 'admin_products_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        $product = $this->entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            $this->addFlash('error', 'Le produit demandé n\'existe pas.');
            return $this->redirectToRoute('admin_products');
        }

        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($product);
            $this->entityManager->flush();
            $this->addFlash('success', 'Produit supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_products');
    }

    ////////////////////////////////////////////////
    ///////////////////Avis/////////////////////
    ////////////////////////////////////////////////
    #[Route('/admin/reviews', name: 'admin_reviews', methods: ['GET'])]
    public function CRUDAdminReviews(): Response
    {
        $reviews = $this->entityManager->getRepository(Review::class)->findAll();
        return $this->render('admin/review/reviews.html.twig', [
            'reviews' => $reviews,
        ]);
    }
}
