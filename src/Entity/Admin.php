<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AdminRepository::class)
 */
class Admin extends User
{
    public function __construct()
    {
        parent::__construct();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }
}
