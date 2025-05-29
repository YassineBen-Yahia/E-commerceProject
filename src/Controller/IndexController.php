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

        $sort=$request->query->get('sort','ASC');
        $data = $this->indexService->getPaginatedProducts($category, $page, $limit,$sort);

        return $this->render('index.html.twig', [
            'produits' => $data['produits'],
            'category' => $category,
            'page' => $page,
            'limit' => $limit,
            'totalProducts' => $data['totalProducts'],
            'totalPages' => $data['totalPages'],
            'sort' => $sort ?? 'ASC',
        ]);
    }

    #[Route('/index/multiple/{categories}/{PriceMin}/{PriceMax}', name: 'app_index_multiple_categories')]
    public function indexParMultipleCategories(float $PriceMin,float $PriceMax,string $categories, Request $request): Response
    {

        $page = max(1, $request->query->getInt('page', 1));
        $limit = $request->query->getInt('limit', 10);
        $sort=$request->query->get('sort','ASC');

        $data = $this->indexService->getPaginatedProductsByFilter($categories, $page, $limit,$PriceMin, $PriceMax,$sort);

        return $this->render('index.html.twig', [
            'produits' => $data['produits'],
            'category' => $categories,
            'page' => $page,
            'limit' => $limit,
            'totalProducts' => $data['totalProducts'],
            'totalPages' => $data['totalPages'],
            'sort' => $sort ?? 'ASC',
            'PriceMin' => $PriceMin,
            'PriceMax' => $PriceMax,
        ]);
    }
    #[Route('/index/search/{search}', name: 'app_index_search')]
    public function indexParSearch(string $search, Request $request): Response
    {

        $sort=$request->query->get('sort','ASC');
        $page = max(1, $request->query->getInt('page', 1));
        $limit = $request->query->getInt('limit', 10);



        $data = $this->indexService->getPaginatedProductsBySearch($search, $page, $limit,$sort);

        return $this->render('index.html.twig', [
            'produits' => $data['produits'],
            'category' => $search,
            'page' => $page,
            'limit' => $limit,
            'totalProducts' => $data['totalProducts'],
            'totalPages' => $data['totalPages'],
            'sort' => $sort ?? 'ASC',
        ]);

    }
}
