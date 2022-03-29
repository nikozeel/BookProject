<?php

namespace App\Entity;

use App\Repository\BookTranslationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=BookTranslationRepository::class)
 * @UniqueEntity("context")
 */
class BookTranslation
{
    public const LANG_RUSSIAN = 0;
    public const LANG_DUTCH = 1;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=2, nullable=false)
     * @Groups({"create_book"})
     */
    private $locale;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     * @Assert\Length(
     *  min=2,
     *  max=60
     * )
     * @Groups({"create_book"})
     */
    private $context;

    /**
     * @ORM\ManyToOne(targetEntity=Book::class, inversedBy="translations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $book;

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

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): self
    {
        $this->book = $book;

        return $this;
    }
}
