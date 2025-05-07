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

            $categories = $this->categoryRepository->findAll();

            // Extract just the names if needed
            $categoryNames = [];
            foreach ($categories as $category) {
                $categoryNames[] = $category->getName();
            }



        return [
        'ThreeProducts' => $topThreeProducts,
        'categories' => $categoryNames,

    ];
}
}
