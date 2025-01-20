<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class PhysicalProduct extends Product
{
    #[ORM\Column(length: 255)]
    private ?string $weight = null;

    #[ORM\Column(length: 255)]
    private ?string $dimensions = null;

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(string $weight): static
    {
        $this->weight = $weight;
        return $this;
    }


    public function getDimensions(): ?string
    {
        return $this->dimensions;
    }


    public function setDimensions(string $dimensions): static
    {
        $this->dimensions = $dimensions;
        return $this;
    }
}
