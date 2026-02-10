<?php

namespace App\Controller;

use App\Entity\Review;
use App\Repository\ProductoRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reviews')]
final class ReviewController extends AbstractController
{
    #[Route('', name: 'app_reviews')]
    public function index(
        Request $request,
        ProductoRepository $productoRepository,
        ReviewRepository $reviewRepository
    ): Response {
        // Paginación
        $offset = (int) $request->query->get('offset', 0);
        $limit = 20;

        // Filtrado por product_type
        $currentType = $request->query->get('type');

        // Tipos de productos distintos para los filtros
        $productTypes = $productoRepository->createQueryBuilder('p')
            ->select('DISTINCT p.productType')
            ->where('p.productType IS NOT NULL')
            ->getQuery()
            ->getResult();

        $productTypes = array_map(fn($p) => $p['productType'], $productTypes);

        // Productos según filtro y paginación
        $qb = $productoRepository->createQueryBuilder('p');

        if ($currentType) {
            $qb->andWhere('p.productType = :type')
                ->setParameter('type', $currentType);
        }

        $productos = $qb->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        // Calculamos la media de estrellas para cada producto
        $productosConMedia = [];
        foreach ($productos as $producto) {
            $avgStars = $reviewRepository->createQueryBuilder('r')
                ->select('AVG(r.stars) as avgStars')
                ->where('r.producto = :producto')
                ->setParameter('producto', $producto)
                ->getQuery()
                ->getSingleScalarResult();

            $productosConMedia[] = [
                'producto' => $producto,
                'avgStars' => $avgStars ?: 0
            ];
        }

        return $this->render('review/review.html.twig', [
            'productos' => $productosConMedia,
            'productTypes' => $productTypes,
            'currentType' => $currentType,
            'offset' => $offset,
            'limit' => $limit
        ]);
    }

    #[Route('/product/{id}', name: 'app_review_product')]
    public function productDetail(
        int $id,
        Request $request,
        ProductoRepository $productoRepository,
        ReviewRepository $reviewRepository,
        EntityManagerInterface $em
    ): Response {
        $producto = $productoRepository->find($id);

        if (!$producto) {
            throw $this->createNotFoundException('Producto no encontrado.');
        }

        $this->denyAccessUnlessGranted('ROLE_USER');

        // Buscamos si el usuario ya tiene una review de este producto
        $existingReview = $reviewRepository->findOneBy([
            'usuario' => $this->getUser(),
            'producto' => $producto
        ]);

        if ($request->isMethod('POST')) {
            $stars = (int) $request->request->get('stars');
            $reviewText = $request->request->get('review');

            if ($existingReview) {
                // Actualizamos la review existente
                $existingReview->setStars($stars);
                $existingReview->setReviewsText($reviewText);
            } else {
                // Creamos una nueva review
                $existingReview = new Review();
                $existingReview->setUsuario($this->getUser());
                $existingReview->setProducto($producto);
                $existingReview->setStars($stars);
                $existingReview->setReviewsText($reviewText);
            }

            $em->persist($existingReview);
            $em->flush();

            $this->addFlash('success', '¡Tu review se ha guardado correctamente!');
            return $this->redirectToRoute('app_review_product', ['id' => $producto->getId()]);
        }

        // Obtener todas las reviews del producto
        $reviews = $reviewRepository->findBy(
            ['producto' => $producto],
            ['id' => 'DESC']
        );

        // Calcular media de estrellas
        $avgStars = $reviewRepository->createQueryBuilder('r')
            ->select('AVG(r.stars) as avgStars')
            ->where('r.producto = :producto')
            ->setParameter('producto', $producto)
            ->getQuery()
            ->getSingleScalarResult();

        return $this->render('review/show.html.twig', [
            'producto' => $producto,
            'reviews' => $reviews,
            'avgStars' => $avgStars ?: 0,
            'existingReview' => $existingReview,
        ]);
    }
}
