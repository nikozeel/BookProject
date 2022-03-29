<?php

namespace App\Service;

use App\Entity\Author;
use App\Entity\AuthorTranslation;
use Doctrine\ORM\EntityManagerInterface;

class NewAuthorCreate
{
    private EntityManagerInterface $_em;

    public function __construct(EntityManagerInterface $_em)
    {
        $this->_em = $_em;
    }

    public function create(string $authorNameEn, string $authorNameRu)
    {
        $newAuthor = new Author();
        $newAuthor->setName($authorNameEn);
        $newAuthorTranslation = new AuthorTranslation();
        $newAuthorTranslation->setLocale("ru");
        $newAuthorTranslation->setContext($authorNameRu);
        $newAuthor->addTranslation($newAuthorTranslation);
        $this->_em->persist($newAuthorTranslation);
        $this->_em->persist($newAuthor);

        return $newAuthor;
    }
}
