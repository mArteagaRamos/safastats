<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
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
        CategoryRepository $categoryRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $products = $productoRepository->findAll();
        $categories = $categoryRepository->findAll();

        if ($request->isMethod('POST') && !$request->query->get('edit')) {
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

            return $this->redirectToRoute('categories');
        }

        return $this->render('category/categories.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'editing' => false,
            'categoryToEdit' => null
        ]);
    }

    #[Route('/edit/{id}', name: 'categories_edit')]
    public function edit(
        int $id,
        Request $request,
        ProductoRepository $productoRepository,
        CategoryRepository $categoryRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $category = $categoryRepository->find($id);
        if (!$category) {
            throw $this->createNotFoundException('Categoría no encontrada');
        }

        $products = $productoRepository->findAll();
        $categories = $categoryRepository->findAll();

        //Actualizar categoría
        if ($request->isMethod('POST')) {
            $category->setName($request->request->get('name'));
            $category->setImage($request->request->get('image'));

            foreach ($category->getProductos() as $prod) {
                $category->removeProducto($prod);
            }

            //Añadir productos
            $selectedProducts = $request->request->all('items');
            foreach ($selectedProducts as $productId) {
                $product = $productoRepository->find($productId);
                if ($product) {
                    $category->addProducto($product);
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Categoría actualizada correctamente');
            return $this->redirectToRoute('categories');
        }

        return $this->render('category/categories.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'editing' => true,
            'categoryToEdit' => $category
        ]);
    }

    #[Route('/delete/{id}', name: 'categories_delete')]
    public function delete(
        int $id,
        CategoryRepository $categoryRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $category = $categoryRepository->find($id);
        if (!$category) {
            throw $this->createNotFoundException('Categoría no encontrada');
        }

        $entityManager->remove($category);
        $entityManager->flush();

        $this->addFlash('success', 'Categoría eliminada correctamente');
        return $this->redirectToRoute('categories');
    }
}
