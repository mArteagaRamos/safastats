<?php

namespace App\Controller;

use App\Entity\Producto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/admin.html.twig');
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[Route('/admin/productos/load', name: 'data_load_api', defaults: ['offset' => 0])]
    public function dataLoad(
        int $offset,
        HttpClientInterface $httpClient,
        EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $limit = 50;

        //Petición a mi API
        $response = $httpClient->request(
            'GET',
            'http://makeup-api.herokuapp.com/api/v1/products.json'
        );

        $content = $response->toArray();

        $chunk = array_slice($content, $offset, $limit);

        foreach ($chunk as $element) {

            // Evitar duplicados
            $existingProduct = $entityManager
                ->getRepository(Producto::class)
                ->findOneBy(['apiId' => $element['id']]);

            if ($existingProduct) {
                continue;
            }

            $producto = new Producto();
            $producto->setApiId($element['id']);
            $producto->setName($element['name']);
            $producto->setBrand($element['brand'] ?? null);
            $producto->setProductType($element['product_type'] ?? null);
            $producto->setProductCategory($element['category'] ?? null);
            $producto->setDescription($element['description'] ?? null);
            $producto->setImageLink($element['image_link'] ?? null);
            $producto->setProductLink($element['product_link'] ?? null);

            $entityManager->persist($producto);
        }

        $entityManager->flush();

        $this->addFlash(
            'success',
            sprintf('Productos cargados: %d a %d', $offset + 1, $offset + count($chunk))
        );

        return $this->render('admin/admin.html.twig', [
            'controller_name' => 'AdminController',
            'content' => $content,
        ]);
    }
}
