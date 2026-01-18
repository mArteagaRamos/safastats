<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        // Ejemplo de productos
        $productos = [
            [
                'id' => 1,
                'name' => 'Labial Rouge',
                'brand' => 'MarcaX',
                'description' => 'Labial mate de larga duración.',
                'imageLink' => 'https://via.placeholder.com/200',
                'rating' => 4,
            ],
            [
                'id' => 2,
                'name' => 'Máscara de pestañas',
                'brand' => 'MarcaY',
                'description' => 'Volumen y definición extrema.',
                'imageLink' => 'https://via.placeholder.com/200',
                'rating' => 5,
            ],
        ];

        return $this->render('product/index.html.twig', [
            'productos' => $productos,
        ]);
    }
}
