<?php

namespace App\Controller;

use App\Repository\ProductoRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReviewController extends AbstractController
{
    #[Route('/reviews', name: 'app_reviews')]
    public function index(
        ProductoRepository $productoRepository,
        Request $request,
    ): Response
    {
        $limit = 20;
        $offset = $request->query->getInt('offset', 0);

        $productos = $productoRepository->findBy(
            [],
            ['id' => 'ASC'],
            $limit,
            $offset
        );

        return $this->render('review/review.html.twig', [
            'productos' => $productos,
            'offset' => $offset,
            'limit' => $limit,
        ]);
    }
}
