<?php

namespace App\Entity;

use App\Repository\RankingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(
    name: 'ranking',
    uniqueConstraints: [
        new ORM\UniqueConstraint(
            name: 'uniq_user_category',
            columns: ['id_usuario', 'id_category']
        )
    ]
)]
#[ORM\Entity(repositoryClass: RankingRepository::class)]
class Ranking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class, inversedBy: 'rankings')]
    #[ORM\JoinColumn(name: 'id_usuario', nullable: false)]
    private ?Usuario $user = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'rankings')]
    #[ORM\JoinColumn(name: 'id_category', nullable: false)]
    private ?Category $category = null;

    /**
     * @var Collection<int, RankingProducto>
     */
    #[ORM\OneToMany(targetEntity: RankingProducto::class, mappedBy: 'ranking', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $rankingProductos;

    public function __construct()
    {
        $this->rankingProductos = new ArrayCollection();
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

    public function getUser(): ?Usuario
    {
        return $this->user;
    }

    public function setUser(?Usuario $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getRankingProductos(): Collection
    {
        return $this->rankingProductos;
    }

    public function addRankingProducto(RankingProducto $rankingProducto): static
    {
        if (!$this->rankingProductos->contains($rankingProducto)) {
            $this->rankingProductos->add($rankingProducto);
            $rankingProducto->setRanking($this);
        }

        return $this;
    }

    public function removeRankingProducto(RankingProducto $rankingProducto): static
    {
        if ($this->rankingProductos->removeElement($rankingProducto)) {
            // set the owning side to null (unless already changed)
            if ($rankingProducto->getRanking() === $this) {
                $rankingProducto->setRanking(null);
            }
        }

        return $this;
    }
}
