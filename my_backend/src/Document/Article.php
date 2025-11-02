<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;

#[ODM\Document(collection: "articles")]
class Article
{
    #[ODM\Id]
    private ?string $id = null;

    #[ODM\Field(type: "string")]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ODM\Field(type: "string")]
    #[Assert\NotBlank]
    private ?string $content = null;

    #[ODM\ReferenceOne(targetDocument: User::class, storeAs: "id")]
    private ?User $author = null;

    #[ODM\Field(type: "date")]
    private ?\DateTimeInterface $publishedAt = null;

    public function __construct()
    {
        $this->publishedAt = new \DateTime();
    }

    public function getId(): ?string
    {
        return (string) $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $t): self
    {
        $this->title = $t;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $c): self
    {
        $this->content = $c;
        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(User $u): self
    {
        $this->author = $u;
        return $this;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTimeInterface $d): self
    {
        $this->publishedAt = $d;
        return $this;
    }
}