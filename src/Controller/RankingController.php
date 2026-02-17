<?php

namespace App\Controller;

use App\Entity\Ranking;
use App\Entity\RankingProducto;
use App\Repository\CategoryRepository;
use App\Repository\ProductoRepository;
use App\Repository\RankingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/rankings')]
final class RankingController extends AbstractController
{
    #[Route('', name: 'rankings_index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('ranking/ranking.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/category/{id}', name: 'rankings_category')]
    public function showCategory(
        int $id,
        CategoryRepository $categoryRepository,
        ProductoRepository $productoRepository,
        RankingRepository $rankingRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $category = $categoryRepository->find($id);
        if (!$category) {
            throw $this->createNotFoundException('Categoría no encontrada');
        }

        $productos = $category->getProductos();

        $ranking = $rankingRepository->findOneBy([
            'user' => $this->getUser(),
            'category' => $category,
        ]);

        $positions = [];
        if ($ranking) {
            foreach ($ranking->getRankingProductos() as $rankingProducto) {
                $positions[$rankingProducto->getProducto()->getId()] = $rankingProducto->getPosition();
            }
        }

        return $this->render('ranking/category.html.twig', [
            'category' => $category,
            'productos' => $productos,
            'positions' => $positions,
        ]);
    }

    #[Route('/category/{id}/save', name: 'rankings_save', methods: ['POST'])]
    public function saveRanking(
        int $id,
        Request $request,
        CategoryRepository $categoryRepository,
        ProductoRepository $productoRepository,
        RankingRepository $rankingRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $category = $categoryRepository->find($id);
        if (!$category) {
            throw $this->createNotFoundException('Categoría no encontrada');
        }

        $ranking = $rankingRepository->findOneBy([
            'user' => $this->getUser(),
            'category' => $category,
        ]);

        if (!$ranking) {
            $ranking = new Ranking();
            $ranking->setUser($this->getUser());
            $ranking->setCategory($category);
            $entityManager->persist($ranking);
        }

        // Limpiar ranking anterior
        foreach ($ranking->getRankingProductos() as $rp) {
            $entityManager->remove($rp);
        }

        // Guardamos nuevo ranking
        $positions = $request->request->all('position');
        if (!is_array($positions)) {
            $positions = [];
        }

        foreach ($positions as $productoId => $pos) {
            $producto = $productoRepository->find((int) $productoId);
            if (!$producto) continue;

            $rankingProducto = new RankingProducto();
            $rankingProducto->setRanking($ranking);
            $rankingProducto->setProducto($producto);
            $rankingProducto->setPosition((int) $pos);

            $entityManager->persist($rankingProducto);
        }

        $entityManager->flush();

        $this->addFlash('success', 'Ranking guardado correctamente');

        return $this->redirectToRoute('rankings_category', [
            'id' => $category->getId(),
        ]);
    }
}
