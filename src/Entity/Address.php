<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La rue ne doit pas être vide.")]
    private ?string $street = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La ville ne doit pas être vide.")]
    private ?string $city = null;

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank(message: "Le code postal ne doit pas être vide.")]
    #[Assert\Regex(
        pattern: '/^\d{5}(-\d{4})?$/',
        message: "Le code postal doit être valide (format: 12345 ou 12345-6789)."
    )]
    private ?string $postalCode = null;

    #[ORM\ManyToOne(inversedBy: 'addresses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct() {

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): static
    {
        $this->street = $street;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;
        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): static
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }
}
