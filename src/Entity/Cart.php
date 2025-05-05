<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, CartItem>
     */
    #[ORM\OneToMany(targetEntity: CartItem::class, mappedBy: 'cart')]
    private Collection $cartItems;

    #[ORM\OneToOne(inversedBy: 'cart')]
    #[ORM\JoinColumn(nullable: true)]
    private ?ClientProfile $ClientProfile = null;

    public function __construct()
    {
        $this->cartItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, CartItem>
     */
    public function getCartItems(): Collection
    {
        return $this->cartItems;
    }

    public function addCartItem(CartItem $cartItem): static
    {
        if (!$this->cartItems->contains($cartItem)) {
            $this->cartItems->add($cartItem);
            $cartItem->setCart($this);
        }

        return $this;
    }

    public function removeCartItem(CartItem $cartItem): void
    {

       $this->cartItems->removeElement($cartItem) ;

    }

    public function getUtilisateur(): ?ClientProfile
    {
        return $this->ClientProfile;
    }

    public function setUtilisateur(ClientProfile $Utilisateur): static
    {
        $this->ClientProfile = $Utilisateur;

        return $this;
    }
}
