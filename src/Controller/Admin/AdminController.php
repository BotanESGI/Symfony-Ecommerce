<?php

namespace App\Controller\Admin;

use App\Controller\IsGranted;
use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    ///////////////////////////////////////////////
    ///
    #[Route('/admin/products', name: 'admin_products', methods: ['GET'])]
    public function CRUDAdminProducts(): Response
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll();
        return $this->render('admin/product/products.html.twig', [
            'products' => $products,
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

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Produit modifié avec succès.');
            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/products/products_create_or_edit.html.twig', [
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
}
