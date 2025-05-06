<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IndexController extends AbstractController
{
    private ProduitRepository $produitRepository;

    public function __construct(ProduitRepository $produitRepository)
    {
        $this->produitRepository = $produitRepository;
    }
    #[Route('/index', name: 'app_index')]
    public function index(): Response
    {

        $produits = $this->produitRepository->findAll();
        return $this->render('index.html.twig', [
            'produits' => $produits,
            'category' => null,
            'controller_name' => 'IndexController',
        ]);
    }
    #[Route('/index/{category}', name: 'app_index_category')]
    public function indexParCategory( string $category ): Response
    {
        $produits = $this->produitRepository->findByCategory($category);
        return $this->render('index.html.twig', [
            'produits' => $produits,
            'category' => $category,
            'controller_name' => 'IndexController',

        ]);
    }
    #[Route('/index/multiple/{categories}', name: 'app_index_multiple_categories')]
    public function indexParMultipleCategories(string $categories): Response
    {
        $categoryArray = explode(',', $categories);
        $produits = $this->produitRepository->findByMultipleCategories($categoryArray);
        return $this->render('index.html.twig', [
            'produits' => $produits,
            'category' => $categories,
            'controller_name' => 'IndexController',
        ]);
    }
}
