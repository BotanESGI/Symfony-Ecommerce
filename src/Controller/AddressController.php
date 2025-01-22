<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddressController extends AbstractController
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    #[Route('/address/create', name: 'create_address', methods: ['GET', 'POST'])]
    public function createAddress(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si l'utilisateur est banni
        if ($this->isGranted('ROLE_BANNED')) {
            return $this->redirectToRoute('default');
        }

        // Vérifier si l'utilisateur est déjà connecté
        if (!$this->getUser()) {
            $referer = $request->headers->get('referer');

            if (!$referer) {
                $referer = $this->generateUrl('home_page');
            }
            return $this->redirect($referer);
        }

        //Récupere le panier
        $cartItems = $this->cartService->getCartItems();
        $cartTotal = $this->cartService->getCartTotal();

        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address->setUser($this->getUser());
            $entityManager->persist($address);
            $entityManager->flush();

            $this->addFlash('success', 'Adresse ajoutée avec succès.');
            return $this->redirectToRoute('select_address');
        }

        return $this->render('address/create.html.twig', [
            'form' => $form->createView(),
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
        ]);
    }


}
