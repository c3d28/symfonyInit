<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PcmCyclistsRepository")
 */
class PcmCyclists
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $IDCyclist;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="integer")
     */
    private $fkIDteam;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $fkIDregion;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $birthdate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $popularity;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $size;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $weight;

    /**
     * @ORM\Column(type="integer")
     */
    private $charac_i_plain;

    /**
     * @ORM\Column(type="integer")
     */
    private $charac_i_mountain;

    /**
     * @ORM\Column(type="integer")
     */
    private $charac_i_downhilling;

    /**
     * @ORM\Column(type="integer")
     */
    private $charac_i_cobble;

    /**
     * @ORM\Column(type="integer")
     */
    private $charac_i_timetrial;

    /**
     * @ORM\Column(type="integer")
     */
    private $charc_i_prologue;

    /**
     * @ORM\Column(type="integer")
     */
    private $charac_i_sprint;

    /**
     * @ORM\Column(type="integer")
     */
    private $charac_i_acceleration;

    /**
     * @ORM\Column(type="integer")
     */
    private $charac_i_endurance;

    /**
     * @ORM\Column(type="integer")
     */
    private $charac_i_resistance;

    /**
     * @ORM\Column(type="integer")
     */
    private $charac_i_recuperation;

    /**
     * @ORM\Column(type="integer")
     */
    private $charac_i_hill;

    /**
     * @ORM\Column(type="integer")
     */
    private $charac_i_baroudeur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIDCyclist(): ?int
    {
        return $this->IDCyclist;
    }

    public function setIDCyclist(int $IDCyclist): self
    {
        $this->IDCyclist = $IDCyclist;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getFkIDteam(): ?int
    {
        return $this->fkIDteam;
    }

    public function setFkIDteam(int $fkIDteam): self
    {
        $this->fkIDteam = $fkIDteam;

        return $this;
    }

    public function getFkIDregion(): ?int
    {
        return $this->fkIDregion;
    }

    public function setFkIDregion(?int $fkIDregion): self
    {
        $this->fkIDregion = $fkIDregion;

        return $this;
    }

    public function getBirthdate(): ?string
    {
        return $this->birthdate;
    }

    public function setBirthdate(?string $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getPopularity(): ?int
    {
        return $this->popularity;
    }

    public function setPopularity(?int $popularity): self
    {
        $this->popularity = $popularity;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(?string $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getCharacIPlain(): ?int
    {
        return $this->charac_i_plain;
    }

    public function setCharacIPlain(int $charac_i_plain): self
    {
        $this->charac_i_plain = $charac_i_plain;

        return $this;
    }

    public function getCharacIMountain(): ?int
    {
        return $this->charac_i_mountain;
    }

    public function setCharacIMountain(int $charac_i_mountain): self
    {
        $this->charac_i_mountain = $charac_i_mountain;

        return $this;
    }

    public function getCharacIDownhilling(): ?int
    {
        return $this->charac_i_downhilling;
    }

    public function setCharacIDownhilling(int $charac_i_downhilling): self
    {
        $this->charac_i_downhilling = $charac_i_downhilling;

        return $this;
    }

    public function getCharacICobble(): ?int
    {
        return $this->charac_i_cobble;
    }

    public function setCharacICobble(int $charac_i_cobble): self
    {
        $this->charac_i_cobble = $charac_i_cobble;

        return $this;
    }

    public function getCharacITimetrial(): ?int
    {
        return $this->charac_i_timetrial;
    }

    public function setCharacITimetrial(int $charac_i_timetrial): self
    {
        $this->charac_i_timetrial = $charac_i_timetrial;

        return $this;
    }

    public function getCharcIPrologue(): ?int
    {
        return $this->charc_i_prologue;
    }

    public function setCharcIPrologue(int $charc_i_prologue): self
    {
        $this->charc_i_prologue = $charc_i_prologue;

        return $this;
    }

    public function getCharacISprint(): ?int
    {
        return $this->charac_i_sprint;
    }

    public function setCharacISprint(int $charac_i_sprint): self
    {
        $this->charac_i_sprint = $charac_i_sprint;

        return $this;
    }

    public function getCharacIAcceleration(): ?int
    {
        return $this->charac_i_acceleration;
    }

    public function setCharacIAcceleration(int $charac_i_acceleration): self
    {
        $this->charac_i_acceleration = $charac_i_acceleration;

        return $this;
    }

    public function getCharacIEndurance(): ?int
    {
        return $this->charac_i_endurance;
    }

    public function setCharacIEndurance(int $charac_i_endurance): self
    {
        $this->charac_i_endurance = $charac_i_endurance;

        return $this;
    }

    public function getCharacIResistance(): ?int
    {
        return $this->charac_i_resistance;
    }

    public function setCharacIResistance(int $charac_i_resistance): self
    {
        $this->charac_i_resistance = $charac_i_resistance;

        return $this;
    }

    public function getCharacIRecuperation(): ?int
    {
        return $this->charac_i_recuperation;
    }

    public function setCharacIRecuperation(int $charac_i_recuperation): self
    {
        $this->charac_i_recuperation = $charac_i_recuperation;

        return $this;
    }

    public function getCharacIHill(): ?int
    {
        return $this->charac_i_hill;
    }

    public function setCharacIHill(int $charac_i_hill): self
    {
        $this->charac_i_hill = $charac_i_hill;

        return $this;
    }

    public function getCharacIBaroudeur(): ?int
    {
        return $this->charac_i_baroudeur;
    }

    public function setCharacIBaroudeur(int $charac_i_baroudeur): self
    {
        $this->charac_i_baroudeur = $charac_i_baroudeur;

        return $this;
    }
}
