<?php

namespace App\Controller;

use App\Repository\ProductoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class StatsController extends AbstractController
{
    #[Route('/stats', name: 'app_stats')]
    public function index(ProductoRepository $productoRepository): Response
    {
        $topProductos = $productoRepository->findTopRated(5);

        return $this->render('stats/stats.html.twig', [
            'topProductos' => $topProductos,
        ]);
    }
}
