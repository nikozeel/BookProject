<?php

namespace App\Entity;

use App\Repository\AuthorTranslationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=AuthorTranslationRepository::class)
 * @UniqueEntity("context")
 */
class AuthorTranslation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=2, nullable=false)
     * @Assert\Choice({"ru"})
     * @Groups({"create_author"})
     */
    private $locale;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     * @Assert\Length(
     *  min=2,
     *  max=60
     * )
     * @Groups({"create_author"})
     */
    private $context;

    /**
     * @ORM\ManyToOne(targetEntity=Author::class, inversedBy="translations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function setContext(string $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): self
    {
        $this->author = $author;

        return $this;
    }
}
