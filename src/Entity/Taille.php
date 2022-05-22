<?php

namespace App\Entity;

use App\Repository\TailleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass=TailleRepository::class)
 */
class Taille
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"produit_info"})
     */
    private $libelle;

    /**
     * @ORM\OneToMany(targetEntity=StockTaille::class, mappedBy="taille")
     */
    private $stockTailles;

    public function __construct()
    {
        $this->stockTailles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, StockTaille>
     */
    public function getStockTailles(): Collection
    {
        return $this->stockTailles;
    }

    public function addStockTaille(StockTaille $stockTaille): self
    {
        if (!$this->stockTailles->contains($stockTaille)) {
            $this->stockTailles[] = $stockTaille;
            $stockTaille->setTaille($this);
        }

        return $this;
    }

    public function removeStockTaille(StockTaille $stockTaille): self
    {
        if ($this->stockTailles->removeElement($stockTaille)) {
            // set the owning side to null (unless already changed)
            if ($stockTaille->getTaille() === $this) {
                $stockTaille->setTaille(null);
            }
        }

        return $this;
    }
}
