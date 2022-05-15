<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass=ProduitRepository::class)
 */
class Produit
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"produit_info"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"produit_info"})
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"produit_info"})
     */
    private $image;

    /**
     * @ORM\Column(type="float")
     * @Groups({"produit_info"})
     */
    private $prix;

    /**
     * @ORM\OneToMany(targetEntity=LigneCommande::class, mappedBy="produit", orphanRemoval=true)
     */
    private $ligneCommandes;

    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="produits")
     */
    private $categorie;

    /**
     * @ORM\OneToMany(targetEntity=StockTaille::class, mappedBy="produit")
     */
    private $stockTailles;

    /**
     * @ORM\ManyToMany(targetEntity=Section::class, inversedBy="produits")
     */
    private $Section;

    public function __construct()
    {
        $this->ligneCommandes = new ArrayCollection();
        $this->stockTailles = new ArrayCollection();
        $this->Section = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * @return Collection<int, LigneCommande>
     */
    public function getLigneCommandes(): Collection
    {
        return $this->ligneCommandes;
    }

    public function addLigneCommande(LigneCommande $ligneCommande): self
    {
        if (!$this->ligneCommandes->contains($ligneCommande)) {
            $this->ligneCommandes[] = $ligneCommande;
            $ligneCommande->setProduit($this);
        }

        return $this;
    }

    public function removeLigneCommande(LigneCommande $ligneCommande): self
    {
        if ($this->ligneCommandes->removeElement($ligneCommande)) {
            // set the owning side to null (unless already changed)
            if ($ligneCommande->getProduit() === $this) {
                $ligneCommande->setProduit(null);
            }
        }

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

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
            $stockTaille->setProduit($this);
        }

        return $this;
    }

    public function removeStockTaille(StockTaille $stockTaille): self
    {
        if ($this->stockTailles->removeElement($stockTaille)) {
            // set the owning side to null (unless already changed)
            if ($stockTaille->getProduit() === $this) {
                $stockTaille->setProduit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Section>
     */
    public function getSection(): Collection
    {
        return $this->Section;
    }

    public function addSection(Section $section): self
    {
        if (!$this->Section->contains($section)) {
            $this->Section[] = $section;
        }

        return $this;
    }

    public function removeSection(Section $section): self
    {
        $this->Section->removeElement($section);

        return $this;
    }
}
