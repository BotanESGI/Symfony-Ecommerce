<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductVoter extends Voter
{
    private const EDIT = 'EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::EDIT && $subject instanceof \App\Entity\Product;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // Vérifie si l'utilisateur est connecté
        if (!$user instanceof UserInterface) {
            return false;
        }

        // On vérifie les permissions pour l'EDIT'
        switch ($attribute) {
            case self::EDIT:
                // Seuls l'admin peut modifier (ROLE_ADMIN)
                return in_array('ROLE_ADMIN', $user->getRoles(), true);
        }

        return false;
    }
}
