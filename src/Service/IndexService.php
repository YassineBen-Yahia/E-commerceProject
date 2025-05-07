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

    public function getPaginatedProducts(?string $category, int $page, int $limit): array
    {
        if ($category) {
            $totalProducts = $this->produitRepository->countByCategory($category);
            $produits = $this->produitRepository->findByCategoryAndLimit($category, $limit, ($page - 1) * $limit);
        } else {
            $totalProducts = $this->produitRepository->count([]);
            $produits = $this->produitRepository->findBy([], [], $limit, ($page - 1) * $limit);
        }

        return [
            'produits' => $produits,
            'totalProducts' => $totalProducts,
            'totalPages' => ceil($totalProducts / $limit)
        ];
    }

    public function getPaginatedProductsByCategories(string $categories, int $page, int $limit): array
    {
        if ($categories) {
            $totalProducts = $this->produitRepository->countByCategories($categories);
            $produits = $this->produitRepository->findByCategoriesAndLimit($categories, $limit, ($page - 1) * $limit);
        } else {
            $totalProducts = $this->produitRepository->count([]);
            $produits = $this->produitRepository->findBy([], [], $limit, ($page - 1) * $limit);
        }

        return [
            'produits' => $produits,
            'totalProducts' => $totalProducts,
            'totalPages' => ceil($totalProducts / $limit)
        ];
    }
}
