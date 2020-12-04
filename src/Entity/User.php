<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255 , unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="json",nullable=true)
     */
    private $roles = [];


    /**
     * @ORM\Column(type="string", length=255,unique=true)
     */
    private $mail;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $profilepcm;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }


    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }


    public function getSalt() {
        
    }
    
    public function eraseCredentials() {
    }
    
    public function serialize(){
        return serialize([
            $this->id,
            $this->mail,
            $this->password,
            $this->username 
        ]);
    }

    public function unserialize($string){
        list(
                $this->id,
                $this->mail,
                $this->password,
                $this->username 
            ) = unserialize($string, ['allowed_classes' => false]);
        
        
    }

    public function getProfilepcm(): ?bool
    {
        return $this->profilepcm;
    }

    public function setProfilepcm(?bool $profilepcm): self
    {
        $this->profilepcm = $profilepcm;

        return $this;
    }
}
