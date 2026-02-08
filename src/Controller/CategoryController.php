<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\ProductoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    #[Route('/admin/category', name: 'categories')]
    public function index(
        Request $request,
        ProductoRepository $productoRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('GET')) {

            $products = $productoRepository->findAll();

            return $this->render('category/categories.html.twig', [
                'products' => $products,
            ]);
        }

        //POST
        $category = new Category();
        $category->setName($request->request->get('name'));
        $category->setImage($request->request->get('image'));

        $productSelected = $request->request->all('items');

        foreach ($productSelected as $productId) {
            $product = $productoRepository->find($productId);
            if ($product) {
                $category->addProducto($product);
            }
        }

        $entityManager->persist($category);
        $entityManager->flush();

        $this->addFlash('success', 'Categoría creada correctamente');

        return $this->redirectToRoute('app_admin');
    }
}
