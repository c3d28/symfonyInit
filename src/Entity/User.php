<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
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
     * @ORM\Column(type="string", length=255,unique=true)
     */
    private $mail;

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
    
    public function getRoles() {
        return [
            'ROLE_USER'
        ];
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
}
