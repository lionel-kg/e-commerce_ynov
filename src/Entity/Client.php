<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 */
class Client extends User
{
    /**
     * @ORM\ManyToMany(targetEntity=Adresse::class, inversedBy="clients")
     */
    protected $adresse;

    public function __construct()
    {
        parent::__construct();
        $this->adresse = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return Collection<int, Adresse>
     */
    public function getAdresse(): Collection
    {
        return $this->adresse;
    }

    public function addAdresse(Adresse $adresse): self
    {
        if (!$this->adresse->contains($adresse)) {
            $this->adresse[] = $adresse;
        }

        return $this;
    }

    public function removeAdresse(Adresse $adresse): self
    {
        $this->adresse->removeElement($adresse);

        return $this;
    }
}
