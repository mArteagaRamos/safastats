<?php

namespace App\Controller;

use App\Repository\ProductoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    #[Route('/admin/products', name: 'app_products')]
    public function index(ProductoRepository $productoRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $products = $productoRepository->findAll();

        return $this->render('product/products.html.twig', [
            'controller_name' => 'ProductController',
            'products' => $products,
        ]);
    }
}
