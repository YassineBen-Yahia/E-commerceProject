<?php

namespace App\Entity;

use App\Repository\ClientProfileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientProfileRepository::class)]
class ClientProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Utilisateur $utilisateur = null;

    #[ORM\OneToOne(mappedBy: 'ClientProfile', cascade: ['persist', 'remove'])]
    private ?WishList $wishList = null;

    #[ORM\OneToOne(mappedBy: 'ClientProfile', cascade: ['persist', 'remove'])]
    private ?Cart $cart = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }
    public function setCart(Cart $cart): static
    {
        // set the owning side of the relation if necessary
        if ($cart->getUtilisateur() !== $this) {
            $cart->setUtilisateur($this);
        }

        $this->cart = $cart;

        return $this;
    }
    public function getCart(): ?Cart
    {
        return $this->cart;
    }
    public function getWishList(): ?WishList
    {
        return $this->wishList;
    }

    public function setWishList(WishList $wishList): static
    {
        // set the owning side of the relation if necessary
        if ($wishList->getUtilisateur() !== $this) {
            $wishList->setUtilisateur($this);
        }

        $this->wishList = $wishList;

        return $this;
    }
}
