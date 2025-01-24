<?php

namespace App\Controller\Profile;

use App\Entity\Address;
use App\Form\AddressType;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class AddressController extends AbstractController
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    #[Route('profile/address', name: 'address')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
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

        $addresses = $entityManager->getRepository(Address::class)->findBy(['user' => $this->getUser()]);

        return $this->render('profile/address/address.html.twig', [
            'addresses' => $addresses,
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
        ]);
    }

    #[Route('profile/address/create', name: 'create_address', methods: ['GET', 'POST'])]
    public function createAddress(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
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

            $refererMessage = $session->getFlashBag()->get('referer_from_process_order');
            if (!empty($refererMessage)) {
                $session->getFlashBag()->add('referer_from_process_adress', 'yes');
                return $this->redirectToRoute('process_order');
            } else {
                return $this->redirectToRoute('address');
            }
        }

        return $this->render('profile/address/address_create.html.twig', [
            'form' => $form->createView(),
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
        ]);
    }

    #[Route('profile/address/edit/{id}', name: 'edit_address', methods: ['GET', 'POST'])]
    public function edit(Address $address, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si l'utilisateur est banni
        if ($this->isGranted('ROLE_BANNED')) {
            return $this->redirectToRoute('default');
        }

        $referer = $request->headers->get('referer');

        if (!$referer) {
            $referer = $this->generateUrl('home_page');
        }

        // Vérifier si l'utilisateur est déjà connecté
        if (!$this->getUser()) {
            return $this->redirect($referer);
        }

        // Vérifie si l'utilisateur a le droit de modifier cette adresse
        if ($address->getUser() !== $this->getUser()) {
            return $this->redirect($referer);
        }

        //Récupere le panier
        $cartItems = $this->cartService->getCartItems();
        $cartTotal = $this->cartService->getCartTotal();

        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Adresse modifiée avec succès.');
            return $this->redirectToRoute('address');
        }

        return $this->render('profile/address/address_edit.html.twig', [
            'form' => $form->createView(),
            'address' => $address,
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
        ]);
    }

    #[Route('profile/address/delete/{id}', name: 'delete_address', methods: ['POST'])]
    public function delete(Address $address, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si l'utilisateur est banni
        if ($this->isGranted('ROLE_BANNED')) {
            return $this->redirectToRoute('default');
        }

        $referer = $request->headers->get('referer');

        if (!$referer) {
            $referer = $this->generateUrl('home_page');
        }

        // Vérifier si l'utilisateur est déjà connecté
        if (!$this->getUser()) {
            return $this->redirect($referer);
        }

        // Vérifie si l'utilisateur a le droit de supprimer cette adresse
        if ($address->getUser() !== $this->getUser()) {
            return $this->redirect($referer);
        }

        if ($this->isCsrfTokenValid('delete' . $address->getId(), $request->request->get('_token'))) {
            $entityManager->remove($address);
            $entityManager->flush();

            $this->addFlash('success', 'Adresse supprimée avec succès.');
        }

        return $this->redirectToRoute('address');
    }

}
