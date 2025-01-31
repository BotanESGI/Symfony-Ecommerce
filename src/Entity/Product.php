<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
// Ajout pour les groupes de normalisation/dénormalisation
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'product')]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn(name: "product_type", type: "string")]
#[ORM\DiscriminatorMap(['physical' => PhysicalProduct::class, 'digital' => DigitalProduct::class])]
abstract class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    // Ajout de `#[Groups]` pour permettre l'exposition dans l'API
    #[Groups(['product:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    // Ajout de `#[Groups]` pour exposer le nom dans l'API
    #[Groups(['product:read', 'product:write'])]
    #[Assert\NotBlank(message: "Le nom ne doit pas être vide.")]
    #[Assert\Length(
        min: 3,
        minMessage: "Le nom doit comporter au moins {{ limit }} caractères."
    )]
    private ?string $name = null;

    #[ORM\Column(type: 'text')]
    // Ajout de `#[Groups]` pour exposer la description dans l'API
    #[Groups(['product:read', 'product:write'])]
    #[Assert\NotBlank(message: "La description ne doit pas être vide.")]
    #[Assert\Length(
        min: 4,
        minMessage: "La description doit comporter au moins {{ limit }} caractères."
    )]
    private ?string $description = null;

    #[ORM\Column]
    // Ajout de `#[Groups]` pour exposer le prix dans l'API
    #[Groups(['product:read', 'product:write'])]
    #[Assert\NotBlank(message: "Le prix ne doit pas être vide.")]
    #[Assert\Positive(message: "Le prix doit être un nombre positif.")]
    private ?float $price = null;

    #[ORM\Column(nullable: true)]
    // Ajout de `#[Groups]` pour exposer l'image dans l'API
    #[Groups(['product:read', 'product:write'])]
    private ?string $image = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Review::class)]
    private Collection $reviews;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'products')]
    private Collection $tags;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'products')]
    private Collection $categories;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(nullable: false)]
    // Ajout de `#[Groups]` pour exposer la catégorie par défaut dans l'API
    #[Groups(['product:read', 'product:write'])]
    #[Assert\NotBlank(message: "La category par defaut ne doit pas être vide.")]
    private Category $defaultCategory;

    public function __construct(Category $defaultCategory)
    {
        $this->defaultCategory = $defaultCategory;
        $this->reviews = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getImage(): ?string
    {
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        if ($this->image) {
            return 'images/' . $this->image;
        }

        return null;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setProduct($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            if ($review->getProduct() === $this) {
                $review->setProduct(null);
            }
        }

        return $this;
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addProduct($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): static
    {
        if ($this->tags->removeElement($tag)) {
            if ($tag->getProducts()->contains($this)) {
                $tag->removeProduct($this);
            }
        }

        return $this;
    }

    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->addProduct($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        if ($this->categories->removeElement($category)) {
            if ($category->getProducts()->contains($this)) {
                $category->removeProduct($this);
            }
        }

        return $this;
    }

    public function getDefaultCategory(): Category
    {
        return $this->defaultCategory;
    }

    public function setDefaultCategory(Category $defaultCategory): static
    {
        if ($defaultCategory === null) {
            throw new \InvalidArgumentException("defaultCategory cannot be null.");
        }
        $this->defaultCategory = $defaultCategory;
        return $this;
    }

    public function getProductType(): string
    {
        if ($this instanceof PhysicalProduct) {
            return 'Produit physique';
        } elseif ($this instanceof DigitalProduct) {
            return 'Produit digital';
        }

        return 'Type de produit inconnu';
    }
}
