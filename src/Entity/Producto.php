<?php

namespace App\Entity;

use App\Repository\ProductoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'productos')]
#[ORM\Entity(repositoryClass: ProductoRepository::class)]
class Producto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id')]
    private ?int $id = null;

    #[ORM\Column(name: 'api_id')]
    private ?int $apiId = null;

    #[ORM\Column(name: 'name', length: 200)]
    private ?string $name = null;

    #[ORM\Column(name: 'brand', length: 100, nullable: true)]
    private ?string $brand = null;

    #[ORM\Column(name: 'product_type', length: 50, nullable: true)]
    private ?string $productType = null;

    #[ORM\Column(name: 'product_category', length: 50, nullable: true)]
    private ?string $productCategory = null;

    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'image_link', length: 255, nullable: true)]
    private ?string $imageLink = null;

    #[ORM\Column(name: 'product_link', length: 255, nullable: true)]
    private ?string $productLink = null;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'producto')]
    private Collection $reviews;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getApiId(): ?int
    {
        return $this->apiId;
    }

    public function setApiId(int $apiId): static
    {
        $this->apiId = $apiId;

        return $this;
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

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getProductType(): ?string
    {
        return $this->productType;
    }

    public function setProductType(?string $productType): static
    {
        $this->productType = $productType;

        return $this;
    }

    public function getProductCategory(): ?string
    {
        return $this->productCategory;
    }

    public function setProductCategory(?string $productCategory): static
    {
        $this->productCategory = $productCategory;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImageLink(): ?string
    {
        return $this->imageLink;
    }

    public function setImageLink(?string $imageLink): static
    {
        $this->imageLink = $imageLink;

        return $this;
    }

    public function getProductLink(): ?string
    {
        return $this->productLink;
    }

    public function setProductLink(?string $productLink): static
    {
        $this->productLink = $productLink;

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setUsuario($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getUsuario() === $this) {
                $review->setUsuario(null);
            }
        }

        return $this;
    }
}
