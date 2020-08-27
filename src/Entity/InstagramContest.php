<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InstagramContestRepository")
 */
class InstagramContest
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
     * @ORM\Column(type="string", length=255)
     */
    private $date;

    /**
     * @ORM\Column(type="date")
     */
    private $dateCreation;

    /**
     * @ORM\Column(type="date")
     */
    private $dateDraw;

    /**
     * @ORM\Column(type="boolean")
     */
    private $finished;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $urlPost;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $winnerInstagram;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $owner;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hashtag;

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

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;

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

    public function getDateDraw(): ?\DateTimeInterface
    {
        return $this->dateDraw;
    }

    public function setDateDraw(\DateTimeInterface $dateDraw): self
    {
        $this->dateDraw = $dateDraw;

        return $this;
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

    public function getUrlPost(): ?string
    {
        return $this->urlPost;
    }

    public function setUrlPost(string $urlPost): self
    {
        $this->urlPost = $urlPost;

        return $this;
    }

    public function getWinnerInstagram(): ?string
    {
        return $this->winnerInstagram;
    }

    public function setWinnerInstagram(string $winner): self
    {
        $this->winnerInstagram = $winner;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(User $user): self
    {
        $this->owner = $user;

        return $this;
    }

    public function getHashtag(): ?string
    {
        return $this->hashtag;
    }

    public function setHashtag(?string $hashtag): self
    {
        $this->hashtag = $hashtag;

        return $this;
    }

}
