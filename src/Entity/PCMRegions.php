<?php

namespace App\Entity;

use App\Repository\PCMRegionsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PCMRegionsRepository::class)
 */
class PCMRegions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $IdRegion;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $fkIDCountry;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdRegion(): ?int
    {
        return $this->IdRegion;
    }

    public function setIdRegion(int $IdRegion): self
    {
        $this->IdRegion = $IdRegion;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFkIDCountry(): ?int
    {
        return $this->fkIDCountry;
    }

    public function setFkIDCountry(int $fkIDCountry): self
    {
        $this->fkIDCountry = $fkIDCountry;

        return $this;
    }
}
