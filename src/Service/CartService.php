<?php
namespace App\Service;

use App\Repository\CartRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface; // Import the interface
use App\Entity\User;

class CartService
{
    private CartRepository $cartRepository;
    private TokenStorageInterface $tokenStorage;
    private AuthorizationCheckerInterface $authChecker;

    public function __construct(
        CartRepository $cartRepository,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authChecker
    ) {
        $this->cartRepository = $cartRepository;
        $this->tokenStorage = $tokenStorage;
        $this->authChecker = $authChecker;
    }

    public function getCartItems()
    {
        $token = $this->tokenStorage->getToken();

        if (!$token || !$token->getUser()) {
            return [];
        }

        $user = $token->getUser();

        if (!is_object($user) || !$user instanceof User) {
            return [];
        }

        if ($this->authChecker->isGranted('ROLE_BANNED')) {
            return [];
        }

        $cart = $this->cartRepository->findOneBy(['user' => $user]);

        return $cart ? $cart->getCartItems()->toArray() : [];
    }

    public function getCartTotal(): float
    {
        $token = $this->tokenStorage->getToken();

        if (!$token || !$token->getUser()) {
            return 0.0;
        }

        $user = $token->getUser();

        if (!is_object($user) || !$user instanceof User) {
            return 0.0;
        }

        if ($this->authChecker->isGranted('ROLE_BANNED')) {
            return 0.0;
        }

        $cart = $this->cartRepository->findOneBy(['user' => $user]);

        if ($cart) {
            $total = 0.0;
            foreach ($cart->getCartItems() as $cartItem) {
                $total += $cartItem->getProduct()->getPrice() * $cartItem->getQuantity();
            }
            return $total;
        }

        return 0.0;
    }
}
