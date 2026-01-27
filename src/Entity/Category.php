<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;

#[ORM\Table(name: 'category')]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id')]
    private ?int $id = null;

    #[ORM\Column(name: 'name', length: 100)]
    private ?string $name = null;

    #[ORM\Column(name: 'image', type: Types::TEXT)]
    private ?string $image = null;

    /**
     * @var Collection<int, Producto>
     */
    #[JoinTable(name: 'category_productos')]
    #[JoinColumn(name: 'id_category', referencedColumnName: 'id')]
    #[InverseJoinColumn(name: 'id_producto', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: Producto::class)]
    private Collection $productos;

    public function __construct()
    {
        $this->productos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Producto>
     */
    public function getProductos(): Collection
    {
        return $this->productos;
    }

    public function addProducto(Producto $producto): static
    {
        if (!$this->productos->contains($producto)) {
            $this->productos->add($producto);
        }

        return $this;
    }

    public function removeProducto(Producto $producto): static
    {
        $this->productos->removeElement($producto);

        return $this;
    }
}
