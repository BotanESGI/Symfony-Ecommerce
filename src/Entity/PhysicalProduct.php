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

    public function setCharacteristics(array $characteristics): static
    {
        $this->characteristics = $characteristics;
        return $this;
    }

}
