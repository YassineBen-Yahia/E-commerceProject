<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use App\Repository\ProduitRepository;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    private ProduitRepository $produitRepository;

    public function __construct(ProduitRepository $produitRepository)
    {
    $this->produitRepository = $produitRepository;
    }

    public function getGlobals(): array
    {
        // Exemple : on récupère le dernier produit ajouté

            $products = $this->produitRepository->findAll();
            shuffle($products);
            $topThreeProducts = array_slice($products, 0, 3);



        return [
        'ThreeProducts' => $topThreeProducts,

    ];
}
}
