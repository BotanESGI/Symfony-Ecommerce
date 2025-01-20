<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class DigitalProduct extends Product
{
    #[ORM\Column(length: 255)]
    private ?string $downloadLink = null;

    public function getDownloadLink(): ?string
    {
        return $this->downloadLink;
    }

    public function setDownloadLink(string $downloadLink): static
    {
        $this->downloadLink = $downloadLink;
        return $this;
    }
}
