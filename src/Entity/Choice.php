<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChoiceRepository")
 */
class Choice
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Draw")
     * @ORM\JoinColumn(nullable=false)
     */
    private $draw;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Participant")
     */
    private $participant;

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

    public function getDraw()
    {
        return $this->draw;
    }

    public function setDraw($draw): void
    {
        $this->draw = $draw;
    }

    public function getParticipant()
    {
        return $this->participant;
    }

    public function setParticipant($participant): void
    {
        $this->participant = $participant;
    }
}
