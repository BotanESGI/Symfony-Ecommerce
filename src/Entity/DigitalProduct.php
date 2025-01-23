<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class DigitalProduct extends Product
{
    #[ORM\Column(length: 255)]
    private ?string $downloadLink = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $filesize = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $filetype = null;

    public function getDownloadLink(): ?string
    {
        return $this->downloadLink;
    }

    public function setDownloadLink(string $downloadLink): static
    {
        $this->downloadLink = $downloadLink;
        return $this;
    }

    public function getFilesize(): ?int
    {
        return $this->filesize;
    }

    public function setFilesize(?int $filesize): static
    {
        $this->filesize = $filesize;
        return $this;
    }

    public function getFiletype(): ?string
    {
        return $this->filetype;
    }

    public function setFiletype(?string $filetype): static
    {
        $this->filetype = $filetype;
        return $this;
    }
}
