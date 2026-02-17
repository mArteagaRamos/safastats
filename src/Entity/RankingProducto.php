<?php

namespace App\Entity;

use App\Repository\RankingProductoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(
    name: 'ranking_producto',
    uniqueConstraints: [
        new ORM\UniqueConstraint(
            name: 'uniq_ranking_producto',
            columns: ['id_ranking', 'id_producto']
        ),
        new ORM\UniqueConstraint(
            name: 'uniq_ranking_position',
            columns: ['id_ranking', 'position']
        )
    ]
)]
#[ORM\Entity(repositoryClass: RankingProductoRepository::class)]
class RankingProducto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Producto::class, inversedBy: 'rankingProductos')]
    #[ORM\JoinColumn(name: 'id_producto', nullable: false)]
    private ?Producto $producto = null;

    #[ORM\ManyToOne(targetEntity: Ranking::class, inversedBy: 'rankingProductos')]
    #[ORM\JoinColumn(name: 'id_ranking', nullable: false)]
    private ?Ranking $ranking = null;

    #[ORM\Column(name: 'position')]
    private ?int $position = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getProducto(): ?Producto
    {
        return $this->producto;
    }

    public function setProducto(?Producto $producto): static
    {
        $this->producto = $producto;

        return $this;
    }

    public function getRanking(): ?Ranking
    {
        return $this->ranking;
    }

    public function setRanking(?Ranking $ranking): static
    {
        $this->ranking = $ranking;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }
}
