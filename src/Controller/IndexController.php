<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use App\Service\IndexService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IndexController extends AbstractController
{
    private IndexService $indexService;
    public function __construct(IndexService $indexService){
        $this->indexService = $indexService;
    }

    #[Route('/index', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('index.html.twig', [
            'produits' => $this->indexService->getAllProducts(),
            'controller_name' => 'IndexController',
        ]);
    }
    #[Route('/index/{category}', name: 'app_index_category')]
    public function indexParCategory( string $category ): Response
    {
        return $this->render('index.html.twig', [
            'produits' =>$this->indexService->getProductsByCategory($category),
            'controller_name' => 'IndexController',
        ]);
    }
    #[Route('/index/multiple/{categories}', name: 'app_index_multiple_categories')]
    public function indexParMultipleCategories(string $categories): Response
    {
        ;
        return $this->render('index.html.twig', [
            'produits' => $this->indexService->getProductsByMultipleCategories($categories),
            'controller_name' => 'IndexController',
        ]);
    }
}
