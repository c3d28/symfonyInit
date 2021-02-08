<?php

namespace App\Entity;

use App\Repository\ChoiceOFA2Repository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChoiceOFA2Repository::class)
 */
class ChoiceOFA2
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
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
     * @ORM\OneToOne(targetEntity=ChoiceOFA1::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $choiceOfa1;

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

    public function getDraw(): ?Draw
    {
        return $this->draw;
    }

    public function setDraw(?Draw $draw): self
    {
        $this->draw = $draw;

        return $this;
    }

    public function getChoiceOfa1(): ?ChoiceOFA1
    {
        return $this->choiceOfa1;
    }

    public function setChoiceOfa1(ChoiceOFA1 $choiceOfa1): self
    {
        $this->choiceOfa1 = $choiceOfa1;

        return $this;
    }
}
