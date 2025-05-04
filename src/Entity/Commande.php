<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    /**
     * @var Collection<int, Produit>
     */

    /**
     * @var Collection<int, cartItem>
     */
    #[ORM\ManyToMany(targetEntity: cartItem::class)]
    private Collection $cartItems;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?utilisateur $utilisateur = null;

    public function __construct()
    {
        $this->cartItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }





    /**
     * @return Collection<int, Produit>
     */




    /**
     * @return Collection<int, cartItem>
     */
    public function getCartItems(): Collection
    {
        return $this->cartItems;
    }

    public function addCartItem(cartItem $cartItem): static
    {
        if (!$this->cartItems->contains($cartItem)) {
            $this->cartItems->add($cartItem);
        }

        return $this;
    }

    public function removeCartItem(cartItem $cartItem): static
    {
        $this->cartItems->removeElement($cartItem);

        return $this;
    }

    public function getUtilisateur(): ?utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }
}
