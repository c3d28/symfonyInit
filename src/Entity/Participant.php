<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParticipantRepository")
 */
class Participant
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $subscribed;

    /**
     * @ORM\Column(type="boolean")
     */
    private $owner;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $usernameExpress;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Draw")
     * @ORM\JoinColumn(nullable=false)
     */
    private $draw;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubscribed(): ?bool
    {
        return $this->subscribed;
    }

    public function setSubscribed(bool $subscribed): self
    {
        $this->subscribed = $subscribed;

        return $this;
    }

    public function getOwner(): ?bool
    {
        return $this->owner;
    }

    public function setOwner(bool $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getUsernameExpress(): ?string
    {
        return $this->usernameExpress;
    }

    public function setUsernameExpress(?string $usernameExpress): self
    {
        $this->usernameExpress = $usernameExpress;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDraw(): ?Draw
    {
        return $this->draw;
    }

    public function setDraw(Draw $draw): self
    {
        $this->draw = $draw;
        return $this;
    }
}
