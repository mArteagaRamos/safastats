<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\RankingProductoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/stats')]
class StatsController extends AbstractController
{
    #[Route('', name: 'app_stats')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('stats/stats.html.twig', [
            'categories' => $categoryRepository->findAll()
        ]);
    }

    #[Route('/category/{id}', name: 'stats_category')]
    public function category(
        int $id,
        CategoryRepository $categoryRepository,
        RankingProductoRepository $rankingProductoRepository
    ): Response {
        $category = $categoryRepository->find($id);
        if (!$category) {
            throw $this->createNotFoundException('Categoría no encontrada');
        }

        $productos = $category->getProductos();

        $stats = [];
        foreach ($productos as $producto) {
            $rankings = $rankingProductoRepository->findBy(['producto' => $producto]);

            if (count($rankings) === 0) {
                continue;
            }

            $avgPosition = array_sum(array_map(fn($rp) => $rp->getPosition(), $rankings)) / count($rankings);

            $stats[] = [
                'producto' => $producto,
                'avgPosition' => $avgPosition,
            ];
        }

        usort($stats, fn($a, $b) => $a['avgPosition'] <=> $b['avgPosition']);

        return $this->render('stats/category.html.twig', [
            'category' => $category,
            'stats' => $stats,
        ]);
    }
}
