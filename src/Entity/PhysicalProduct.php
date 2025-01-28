<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class PhysicalProduct extends Product
{
    #[ORM\Column(type: "json", nullable: true)]
    #[Groups(['product:read', 'product:write'])] // Ajout de la sérialisation pour l'API
    private ?array $characteristics = null;

    public function getCharacteristics(): ?array
    {
        return $this->characteristics;
    }

    public function setCharacteristics($characteristics): static
    {
        if (is_string($characteristics)) {
            $decoded = json_decode($characteristics, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $this->characteristics = $decoded;
            } else {
                $this->characteristics = [];
            }
        } elseif (is_array($characteristics)) {
            $this->characteristics = $characteristics;
        } else {
            $this->characteristics = [];
        }

        return $this;
    }

    // Ajout de compatibilité avec "features" pour résoudre l'erreur
    public function getFeatures(): ?array
    {
        return $this->getCharacteristics();
    }

    public function setFeatures(?array $features): static
    {
        return $this->setCharacteristics($features);
    }
}
