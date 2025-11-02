<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ODM\Document(collection="users")
 */
class User
{
    /**
     * @ODM\Id
     */
    private $id;

    /**
     * @ODM\Field(type="string")
     * @ODM\Index(unique=true)
     * @Assert\NotBlank
     * @Assert\Email
     */
    private $email;

    /**
     * @ODM\Field(type="string")
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ODM\Field(type="string")
     */
    private $password; // hashed

    public function getId(): ?string { return (string) $this->id; }
    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }
    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $pw): self { $this->password = $pw; return $this; }
}