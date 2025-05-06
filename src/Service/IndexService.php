<?php

namespace App\Service;

use App\Repository\ProduitRepository;

class IndexService
{
    private ProduitRepository $produitRepository;
    public function __construct(ProduitRepository $produitRepository)
    {
        $this->produitRepository = $produitRepository;
    }
    public function getAllProducts(): ?array{
        return $this->produitRepository->findAll();

    }
    public function getProductsByCategory(string $category): ?array{
        return $this->produitRepository->findByCategory($category);
    }
    public function getProductsByMultipleCategories(string $categories): ?array{
        $categoryArray = explode(',', $categories);
        return $this->produitRepository->findByMultipleCategories($categoryArray);
    }

}