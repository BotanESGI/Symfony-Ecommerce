<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: "L'email ne doit pas être vide.")]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas un email valide.")]
    private ?string $email = null;


    #[ORM\Column]
    #[Assert\Length(
        min: 8,
        minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: '/[A-Z]/',
        message: 'Le mot de passe doit contenir au moins une lettre majuscule.'
    )]
    #[Assert\Regex(
        pattern: '/[0-9]/',
        message: 'Le mot de passe doit contenir au moins un chiffre.'
    )]
    #[Assert\Regex(
        pattern: '/[\W_]/',
        message: 'Le mot de passe doit contenir au moins un caractère spécial.'
    )]
    private ?string $password = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "le role ne doit pas être vide.")]
    private array $roles = [];


    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le champ nom ne doit pas être vide.")]
    #[Assert\Length(min: 3, minMessage: "Le nom doit contenir au moins {{ limit }} caractères.")]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le champ prénom ne doit pas être vide.")]
    #[Assert\Length(min: 3, minMessage: "Le prénom doit contenir au moins {{ limit }} caractères.")]
    private ?string $lastname = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $resetToken = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $confirmationToken = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'user', cascade: ['remove'], orphanRemoval: true)]
    private Collection $reviews;

    #[ORM\OneToMany(targetEntity: Cart::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    private Collection $carts;

    #[ORM\OneToMany(targetEntity: Invoice::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    private Collection $invoices;

    #[ORM\OneToMany(targetEntity: Address::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    private Collection $addresses;

    #[ORM\OneToMany(targetEntity: Orders::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    private Collection $orders;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
        $this->carts = new ArrayCollection();
        $this->invoices = new ArrayCollection();
        $this->addresses = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->roles = ['ROLE_USER'];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function setPasswordChange(?string $password): static
    {
        if (!empty($password)) {
            $this->password = password_hash($password, PASSWORD_BCRYPT);
        }
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
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
            $review->setUser($this);
        }
        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            if ($review->getUser() === $this) {
                $review->setUser(null);
            }
        }
        return $this;
    }

    public function getCarts(): Collection
    {
        return $this->carts;
    }

    public function addCart(Cart $cart): static
    {
        if (!$this->carts->contains($cart)) {
            $this->carts->add($cart);
            $cart->setUser($this);
        }
        return $this;
    }

    public function removeCart(Cart $cart): static
    {
        if ($this->carts->removeElement($cart)) {
            if ($cart->getUser() === $this) {
                $cart->setUser(null);
            }
        }
        return $this;
    }

    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): static
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices->add($invoice);
            $invoice->setUser($this);
        }
        return $this;
    }

    public function removeInvoice(Invoice $invoice): static
    {
        if ($this->invoices->removeElement($invoice)) {
            if ($invoice->getUser() === $this) {
                $invoice->setUser(null);
            }
        }
        return $this;
    }

    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Address $address): static
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses->add($address);
            $address->setUser($this);
        }
        return $this;
    }

    public function removeAddress(Address $address): static
    {
        if ($this->addresses->removeElement($address)) {
            if ($address->getUser() === $this) {
                $address->setUser(null);
            }
        }
        return $this;
    }

    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Orders $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setUser($this);
        }
        return $this;
    }

    public function removeOrder(Orders $order): static
    {
        if ($this->orders->removeElement($order)) {
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
        // Clear sensitive data if needed
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

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;
        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;
        return $this;
    }

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;
        return $this;
    }
}
