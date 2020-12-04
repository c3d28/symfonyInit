<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChoicePositionRepository")
 */
class ChoiceChristmas
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Christmas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $christmas;

    /**
     * @ORM\Column(type="date",nullable=true)
     */
    private $dateDraw;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $giftTo;

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

    public function getChristmas()
    {
        return $this->christmas;
    }

    public function setChristmas($christmas): void
    {
        $this->christmas = $christmas;
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

    public function getGiftTo(): ?string
    {
        return $this->giftTo;
    }

    public function setGiftTo(?string $giftTo): self
    {
        $this->giftTo = $giftTo;

        return $this;
    }



}
