<?php

namespace App\Controller\Product;

use App\Controller\AccessDeniedException;
use App\Entity\Product;
use App\Entity\Review;
use App\Enum\ReviewStatusEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends AbstractController
{
    #[Route('/product/{id}/add-review', name: 'add_review', methods: ['POST'])]
    public function addReview(Request $request, Product $product, int $id, EntityManagerInterface $em): Response
    {
        //Récupere la page par laquelle il est venue
        $referer = $request->headers->get('referer');

        if (!$referer) {
            $referer = $this->generateUrl('home_page');
        }

        // Vérifie si l'utilisateur est banni
        if ($this->isGranted('ROLE_BANNED')) {
            return $this->redirectToRoute('default');
        }

        // Vérifie si l'utilisateur est connecté
        if (!$this->getUser()) {
            $this->addFlash('warning', 'Vous devez être connecté pour soumettre un avis.');
            return $this->redirectToRoute('login');
        }

        // Vérifie le produit
        $product = $em->getRepository(Product::class)->find($id);
        if (!$product) {
            $this->addFlash('error', 'Le produit demandé est introuvable.');
            return $this->redirectToRoute('product_page');
        }

        // Récupère le contenu et la note de l'avis
        $content = $request->request->get('content');
        $rating = $request->request->get('rating');

        if ($content && $rating) {
            $review = new Review();
            $review->setContent($content);
            $review->setRating((int)$rating);
            $review->setUser($this->getUser());
            $review->setProduct($product);
            $review->setStatus(ReviewStatusEnum::PENDING);
            $review->setDatePublication(new \DateTime());

            $em->persist($review);
            $em->flush();

            $this->addFlash('success', 'Votre avis a été soumis avec succès et est en attente de validation.');
        } else {
            $this->addFlash('error', 'Tous les champs sont obligatoires.');
        }

        return $this->redirect($referer);
    }

    #[Route('/product/{id}/{reviewId}/delete-review', name: 'delete_review', methods: ['POST'])]
    public function deleteReview(Request $request, Product $product, int $reviewId, EntityManagerInterface $em): Response
    {
        //Récupere la page par laquelle il est venue
        $referer = $request->headers->get('referer');

        if (!$referer) {
            $referer = $this->generateUrl('home_page');
        }

        // Vérifie si l'utilisateur est banni
        if ($this->isGranted('ROLE_BANNED')) {
            return $this->redirectToRoute('default');
        }

        //Verifie si connecté
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('warning', 'Vous devez être connecté pour supprimer un avis.');
            return $this->redirectToRoute('login');
        }

        $review = $em->getRepository(Review::class)->find($reviewId);
        if (!$review) {
            $this->addFlash('error', 'L\'avis demandé est introuvable.');
            return $this->redirectToRoute('product_page');
        }

        if ($review->getUser() !== $user) {
            throw new AccessDeniedException('Vous n\'êtes pas autorisé à supprimer cet avis.');
        }

        $em->remove($review);
        $em->flush();

        $this->addFlash('success', 'Votre avis a été supprimé avec succès.');

        return $this->redirect($referer);
    }


}
