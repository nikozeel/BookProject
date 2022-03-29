<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 * @UniqueEntity("name")
 */
class Book
{
    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if (count($this->getTranslations()) != 2) {
            $context->buildViolation('The book must have exactly 2 translations')
            ->atPath('translations')
            ->addViolation();

            return;
        }

        if ($this->getTranslations()[BookTranslation::LANG_RUSSIAN]->getLocale() !== "ru" || $this->getTranslations()[BookTranslation::LANG_DUTCH]->getLocale() !== "de") {
            $context->buildViolation('Wrong order or type of translations')
                ->atPath('translations')
                ->addViolation();
        }
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     * @Assert\Length(
     *  min=2,
     *  max=60
     * )
     * @Groups({"create_book"})
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=BookTranslation::class, mappedBy="book", cascade={"persist"})
     * @Assert\Valid
     * @Groups({"create_book"})
     */
    private $translations;

    /**
     * @ORM\ManyToOne(targetEntity=Author::class, inversedBy="books")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"create_book"})
     */
    private $author;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
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
     * @return Collection<int, BookTranslation>
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function addTranslation(BookTranslation $translation): self
    {
        if (!$this->translations->contains($translation)) {
            $this->translations[] = $translation;
            $translation->setBook($this);
        }

        return $this;
    }

    public function removeTranslation(BookTranslation $translation): self
    {
        if ($this->translations->removeElement($translation)) {
            // set the owning side to null (unless already changed)
            if ($translation->getBook() === $this) {
                $translation->setBook(null);
            }
        }

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
