<?php

namespace App\Entity;

use App\Repository\ChoiceOFA1Repository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChoiceOFA1Repository::class)
 */
class ChoiceOFA1
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
     * @ORM\OneToOne(targetEntity=ChoiceOFA2::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $choiceOfa2;

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

    public function getChoiceOfa2(): ?ChoiceOFA2
    {
        return $this->choiceOfa2;
    }

    public function setChoiceOfa2(ChoiceOFA2 $choiceOfa2): self
    {
        $this->choiceOfa2 = $choiceOfa2;

        return $this;
    }
}
