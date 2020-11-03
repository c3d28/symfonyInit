<?php

namespace App\Entity;

use App\Repository\PCMCountryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PCMCountryRepository::class)
 */
class PCMCountry
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
    private $IDcountry;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $CONSTANT;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $gene_sz_flag;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIDcountry(): ?int
    {
        return $this->IDcountry;
    }

    public function setIDcountry(int $IDcountry): self
    {
        $this->IDcountry = $IDcountry;

        return $this;
    }

    public function getCONSTANT(): ?string
    {
        return $this->CONSTANT;
    }

    public function setCONSTANT(string $CONSTANT): self
    {
        $this->CONSTANT = $CONSTANT;

        return $this;
    }

    public function getGeneSzFlag(): ?string
    {
        return $this->gene_sz_flag;
    }

    public function setGeneSzFlag(string $gene_sz_flag): self
    {
        $this->gene_sz_flag = $gene_sz_flag;

        return $this;
    }
}
