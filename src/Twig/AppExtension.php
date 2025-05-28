<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use App\Repository\CategorieRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use App\Repository\ProduitRepository;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    private ProduitRepository $produitRepository;
    private CategorieRepository $categoryRepository;

    public function __construct(ProduitRepository $produitRepository, CategorieRepository $categoryRepository)
    {
    $this->produitRepository = $produitRepository;
    $this->categoryRepository = $categoryRepository;
    }

    public function getGlobals(): array
    {
        // Exemple : on récupère le dernier produit ajouté

            $products = $this->produitRepository->findAll();
            shuffle($products);
            $topThreeProducts = array_slice($products, 0, 3);

            $stocks = $this->categoryRepository->findStockInCategory();

            // Extract just the names if needed




        return [
        'ThreeProducts' => $topThreeProducts,
        'categories' => $stocks,

    ];
}
}
