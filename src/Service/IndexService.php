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

    public function getPaginatedProducts(?string $category, int $page, int $limit,string $sort): array
    {
        if ($category) {
            $totalProducts = $this->produitRepository->countByCategory($category);
            $produits = $this->produitRepository->findByCategoryAndLimit($category, $limit, ($page - 1) * $limit,$sort);
        } else {
            $totalProducts = $this->produitRepository->count([]);
            $produits = $this->produitRepository->findBy([], ['price'=>$sort], $limit, ($page - 1) * $limit);
        }

        return [
            'produits' => $produits,
            'totalProducts' => $totalProducts,
            'totalPages' => ceil($totalProducts / $limit)
        ];
    }

    public function getPaginatedProductsByFilter(string $categories, int $page, int $limit,float $min, float $max,string $sort): array
    {
        if ($categories!=='all') {
            $totalProducts = $this->produitRepository->countByCategories($categories);
            $produits = $this->produitRepository->findByFilterAndLimit($categories, $limit, ($page - 1) * $limit, $min, $max,$sort);
        } else {
            $totalProducts = $this->produitRepository->count([]);
            $produits=$this->produitRepository->findByPriceRange($min, $max,$sort);
                    }

        return [
            'produits' => $produits,
            'totalProducts' => $totalProducts,
            'totalPages' => ceil($totalProducts / $limit)
        ];
    }

    public function getPaginatedProductsBySearch(string $search, int $page, int $limit,string $sort): array
    {
        $totalProducts = $this->produitRepository->countBySearch($search);
        $produits = $this->produitRepository->findBySearch($search, $limit, ($page - 1) * $limit,$sort);

        return [
            'produits' => $produits,
            'totalProducts' => $totalProducts,
            'totalPages' => ceil($totalProducts / $limit)
        ];
    }
}
