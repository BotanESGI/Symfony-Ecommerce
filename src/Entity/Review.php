<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Enum\ReviewStatusEnum;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: "Le contenu ne doit pas être vide.")]
    private ?string $content = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La note ne doit pas être vide.")]
    #[Assert\Range(
        min: 1,
        max: 5,
        notInRangeMessage: 'La note doit être comprise entre {{ min }} et {{ max }}.'
    )]
    private ?int $rating = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: "L'utilisateur ne doit pas être vide.")]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotBlank(message: "Le produit ne doit pas être vide.")]
    private ?Product $product = null;

    #[ORM\Column(enumType: ReviewStatusEnum::class)]
    #[Assert\NotBlank(message: "Le status ne doit pas être vide.")]
    private ?ReviewStatusEnum $status = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank(message: "La date de publication ne doit pas être vide.")]
    private ?\DateTimeInterface $datePublication = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;
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

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;
        return $this;
    }

    public function getStatus(): ?ReviewStatusEnum
    {
        return $this->status;
    }

    public function getStatusString(): string
    {
        return $this->status->value;
    }

    public function setStatus(ReviewStatusEnum $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getDatePublication(): ?\DateTimeInterface
    {
        return $this->datePublication;
    }


    public function setDatePublication(\DateTimeInterface $datePublication): static
    {
        $this->datePublication = $datePublication;
        return $this;
    }
}
