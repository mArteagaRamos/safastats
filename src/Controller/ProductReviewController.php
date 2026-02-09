<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Entity\Review;
use App\Repository\ReviewRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductReviewController extends AbstractController
{
    #[Route('/reviews/product/{id}', name: 'app_review_product')]
    public function show(
        Producto $producto,
        ReviewRepository $reviewRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $reviews = $reviewRepository->findBy(
            ['product' => $producto],
            ['id' => 'DESC'],
        );

        //Calcular media de estrellas (valoración)
        $average = null;
        if (count($reviews) > 0) {
            $total = array_sum(array_map(fn($r) => $r->getStars(), $reviews));
            $average = round($total / count($reviews), 1);
        }

        //Guardar la review
        if ($request->isMethod('POST')) {
            $this->denyAccessUnlessGranted('ROLE_USER');

            $existing = $reviewRepository->findOneBy([
                'usuario' => $this->getUser(),
                'product' => $producto,
            ]);

            if (!$existing) {
                $review = new Review();
                $review->setUsuario($this->getUser());
                $review->setProducto($producto);
                $review->setStars((int) $request->request->get('stars'));
                $review->setReviewsText($request->request->get('review'));

                $entityManager->persist($review);
                $entityManager->flush();

                $this->addFlash('danger', 'Ya has valorado este producto.');
            }

            return $this->render('review/show.html.twig', [
                'producto' => $producto,
                'reviews' => $reviews,
                'average' => $average,
            ]);
        }


        return $this->render('product_review/show.html.twig', [
            'controller_name' => 'ProductReviewController',
        ]);
    }
}
