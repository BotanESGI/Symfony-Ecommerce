<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La montant total ne doit pas être vide.")]
    #[Assert\Positive(message: "Le montant doit être un nombre positif.")]
    private ?float $totalAmount = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: "L'utilisateur ne doit pas être vide.")]
    private ?User $user = null;

    #[ORM\OneToOne(targetEntity: Orders::class, inversedBy: 'invoice', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    #[Assert\NotBlank(message: "La commande ne doit pas être vide.")]
    private ?Orders $order = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $pdfPath = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalAmount(): ?float
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(float $totalAmount): static
    {
        $this->totalAmount = $totalAmount;
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

    public function getOrder(): ?Orders
    {
        return $this->order;
    }

    public function setOrder(?Orders $order): static
    {
        $this->order = $order;
        return $this;
    }

    public function getPdfPath(): ?string
    {
        return $this->pdfPath;
    }

    public function setPdfPath(?string $pdfPath): static
    {
        $this->pdfPath = $pdfPath;
        return $this;
    }
}
