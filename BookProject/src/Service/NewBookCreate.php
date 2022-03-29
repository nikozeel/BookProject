<?php

namespace App\Service;

use App\Entity\Author;
use App\Entity\AuthorTranslation;
use App\Entity\Book;
use App\Entity\BookTranslation;
use Doctrine\ORM\EntityManagerInterface;

class NewBookCreate
{
    private EntityManagerInterface $_em;

    public function __construct(EntityManagerInterface $_em)
    {
        $this->_em = $_em;
    }

    public function create(Author $author, array $bookTranslations)
    {
        $book = new Book();
        $book->setAuthor($author);
        $book->setName($bookTranslations["en"]); 
        $bookTranslationRu = new BookTranslation();
        $bookTranslationRu->setLocale("ru");
        $bookTranslationRu->setContext($bookTranslations["ru"]);
        $bookTranslationDe = new BookTranslation();
        $bookTranslationDe->setLocale("de");
        $bookTranslationDe->setContext($bookTranslations["de"]);
        $book->addTranslation($bookTranslationRu);
        $book->addTranslation($bookTranslationDe);
        $this->_em->persist($bookTranslationRu);
        $this->_em->persist($bookTranslationDe);
        $this->_em->persist($book);
    }
}
