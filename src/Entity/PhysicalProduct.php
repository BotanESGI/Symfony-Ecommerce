<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class PhysicalProduct extends Product
{
    #[ORM\Column(type: "json", nullable: true)]
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
}
