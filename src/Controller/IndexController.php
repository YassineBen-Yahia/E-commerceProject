<?php

namespace App\Controller;

use App\Service\IndexService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;



final class IndexController extends AbstractController
{
    private IndexService $indexService;

    public function __construct(IndexService $indexService)
    {
        $this->indexService = $indexService;
    }

    #[Route('/index/{category?}', name: 'app_index')]
    public function index(Request $request, ?string $category = null): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = $request->query->getInt('limit', 20);

        $data = $this->indexService->getPaginatedProducts($category, $page, $limit);

        return $this->render('index.html.twig', [
            'produits' => $data['produits'],
            'category' => $category,
            'page' => $page,
            'limit' => $limit,
            'totalProducts' => $data['totalProducts'],
            'totalPages' => $data['totalPages'],
        ]);
    }

    #[Route('/index/multiple/{categories}', name: 'app_index_multiple_categories')]
    public function indexParMultipleCategories(string $categories, Request $request): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = $request->query->getInt('limit', 10);

        $data = $this->indexService->getPaginatedProductsByCategories($categories, $page, $limit);

        return $this->render('index.html.twig', [
            'produits' => $data['produits'],
            'category' => $categories,
            'page' => $page,
            'limit' => $limit,
            'totalProducts' => $data['totalProducts'],
            'totalPages' => $data['totalPages'],
        ]);
    }
}
