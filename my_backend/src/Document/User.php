<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;

#[ODM\Document(collection: "users")]
#[ODM\Index(keys: ["email" => "asc"], options: ["unique" => true])]
class User
{
    #[ODM\Id]
    private ?string $id = null;

    #[ODM\Field(type: "string")]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[ODM\Field(type: "string")]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ODM\Field(type: "string")]
    private ?string $password = null; // hashed

    public function getId(): ?string
    {
        return (string) $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $pw): self
    {
        $this->password = $pw;
        
        return $this;
    }
}