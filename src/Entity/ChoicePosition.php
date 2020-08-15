<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChoicePositionRepository")
 */
class ChoicePosition
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $text;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Rank")
     * @ORM\JoinColumn(nullable=false)
     */
    private $rank;

    /**
     * @ORM\Column(type="date",nullable=true)
     */
    private $dateDraw;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $place;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getRank()
    {
        return $this->rank;
    }

    public function setRank($rank): void
    {
        $this->rank = $rank;
    }

    public function getDateDraw(): ?\DateTimeInterface
    {
        return $this->dateDraw;
    }

    public function setDateDraw(\DateTimeInterface $dateDraw): self
    {
        $this->dateDraw = $dateDraw;

        return $this;
    }

    public function getPlace(): ?int
    {
        return $this->place;
    }

    public function setPlace(?int $place): self
    {
        $this->place = $place;

        return $this;
    }



}
