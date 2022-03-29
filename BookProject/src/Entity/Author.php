<?php

namespace App\Entity;

use App\Repository\AuthorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=AuthorRepository::class)
 * @UniqueEntity("name")
 */
class Author
{
    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if (count($this->getTranslations()) > 1) {
            $context->buildViolation('Only one translation (ru) available for author entity')
                ->atPath('translations')
                ->addViolation();
        }
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"create_book"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     * @Groups({"create_author"})
     * @Assert\Length(
     *  min=2,
     *  max=60
     * )
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=AuthorTranslation::class, mappedBy="author", cascade={"persist"})
     * @Assert\Valid
     * @Groups({"create_author"})
     */
    private $translations;

    /**
     * @ORM\OneToMany(targetEntity=Book::class, mappedBy="author")
     */
    private $books;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->books = new ArrayCollection();
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

    /**
     * @return Collection<int, AuthorTranslation>
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function addTranslation(AuthorTranslation $translation): self
    {
        if (!$this->translations->contains($translation)) {
            $this->translations[] = $translation;
            $translation->setAuthor($this);
        }

        return $this;
    }

    public function removeTranslation(AuthorTranslation $translation): self
    {
        if ($this->translations->removeElement($translation)) {
            // set the owning side to null (unless already changed)
            if ($translation->getAuthor() === $this) {
                $translation->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
            $book->setAuthor($this);
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        if ($this->books->removeElement($book)) {
            // set the owning side to null (unless already changed)
            if ($book->getAuthor() === $this) {
                $book->setAuthor(null);
            }
        }

        return $this;
    }
}
