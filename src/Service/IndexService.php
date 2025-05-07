<?php

namespace App\Service;

use App\Repository\ProduitRepository;
use phpDocumentor\Reflection\Types\Integer;

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

    public function getPaginatedProductsByFilter(string $categories, int $page, int $limit,float $min, float $max): array
    {
        if ($categories!=='all') {
            $totalProducts = $this->produitRepository->countByCategories($categories);
            $produits = $this->produitRepository->findByFilterAndLimit($categories, $limit, ($page - 1) * $limit, $min, $max);
        } else {
            $totalProducts = $this->produitRepository->count([]);
            $produits=$this->produitRepository->findByPriceRange($min, $max);
                    }

        return [
            'produits' => $produits,
            'totalProducts' => $totalProducts,
            'totalPages' => ceil($totalProducts / $limit)
        ];
    }

    public function getPaginatedProductsBySearch(string $search, int $page, int $limit): array
    {
        $totalProducts = $this->produitRepository->countBySearch($search);
        $produits = $this->produitRepository->findBySearch($search, $limit, ($page - 1) * $limit);

        return [
            'produits' => $produits,
            'totalProducts' => $totalProducts,
            'totalPages' => ceil($totalProducts / $limit)
        ];
    }
}
