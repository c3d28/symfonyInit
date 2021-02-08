<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DrawRepository")
 */
class Draw
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
    private $name;

    /**
     * @ORM\Column(type="date")
     */
    private $dateCreation;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateDraw;

    /**
     * @ORM\Column(type="boolean")
     */
    private $finished;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $shareCode;

    /**
     * @ORM\OneToMany(targetEntity=ChoiceOFA1::class, mappedBy="draw_id", orphanRemoval=true)
     */
    private $choiceOFA1s;

    /**
     * @ORM\OneToMany(targetEntity=ChoiceOFA2::class, mappedBy="draw_id", orphanRemoval=true)
     */
    private $choiceOFA2s;

    public function __construct()
    {
        $this->choiceOFA1s = new ArrayCollection();
        $this->choiceOFA2s = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateDraw()
    {
        return $this->dateDraw;
    }

    /**
     * @param mixed $dateDraw
     */
    public function setDateDraw($dateDraw): void
    {
        $this->dateDraw = $dateDraw;
    }


    public function getFinished(): ?bool
    {
        return $this->finished;
    }

    public function setFinished(bool $finished): self
    {
        $this->finished = $finished;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getShareCode(): ?string
    {
        return $this->shareCode;
    }

    public function setShareCode(?string $shareCode): self
    {
        $this->shareCode = $shareCode;

        return $this;
    }

    /**
     * @return Collection|ChoiceOFA1[]
     */
    public function getChoiceOFA1s(): Collection
    {
        return $this->choiceOFA1s;
    }

    public function addChoiceOFA1(ChoiceOFA1 $choiceOFA1): self
    {
        if (!$this->choiceOFA1s->contains($choiceOFA1)) {
            $this->choiceOFA1s[] = $choiceOFA1;
            $choiceOFA1->setDrawId($this);
        }

        return $this;
    }

    public function removeChoiceOFA1(ChoiceOFA1 $choiceOFA1): self
    {
        if ($this->choiceOFA1s->removeElement($choiceOFA1)) {
            // set the owning side to null (unless already changed)
            if ($choiceOFA1->getDrawId() === $this) {
                $choiceOFA1->setDrawId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ChoiceOFA2[]
     */
    public function getChoiceOFA2s(): Collection
    {
        return $this->choiceOFA2s;
    }

    public function addChoiceOFA2(ChoiceOFA2 $choiceOFA2): self
    {
        if (!$this->choiceOFA2s->contains($choiceOFA2)) {
            $this->choiceOFA2s[] = $choiceOFA2;
            $choiceOFA2->setDrawId($this);
        }

        return $this;
    }

    public function removeChoiceOFA2(ChoiceOFA2 $choiceOFA2): self
    {
        if ($this->choiceOFA2s->removeElement($choiceOFA2)) {
            // set the owning side to null (unless already changed)
            if ($choiceOFA2->getDrawId() === $this) {
                $choiceOFA2->setDrawId(null);
            }
        }

        return $this;
    }
}
