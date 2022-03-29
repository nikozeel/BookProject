<?php

namespace App\Service;

use App\Entity\Book;
use App\Entity\BookTranslation;
use Exception;

class MapResponse
{
    private static function getLocaleKeyForTranslationsContext($_locale)
    {
        switch ($_locale) {
            case "ru":
                return BookTranslation::LANG_RUSSIAN;
                break;
            case "de":
                return BookTranslation::LANG_DUTCH;
                break;
            default:
                throw new Exception("Unknown locale type {$_locale}");
        }
    }

    public static function mapBookSearchResponse($_locale, $queryResult)
    {
        $returnArr = [];

        if ($_locale === "en") {
            foreach ($queryResult as $bookItem) {
                $returnArr[] = [
                    "Id" => $bookItem->getId(),
                    "Name" => $bookItem->getName(),
                    "Author" => [
                        "Id" => $bookItem->getAuthor()->getId(),
                        "Name" => $bookItem->getAuthor()->getName(),
                    ]
                ];
            }
        } else {
            $translationContextKey = self::getLocaleKeyForTranslationsContext($_locale);
            foreach ($queryResult as $bookItem) {

                if ($translationContextKey === BookTranslation::LANG_RUSSIAN) {
                    $authorName = $bookItem->getAuthor()->getTranslations()->getValues()[0]->getContext();
                } else {
                    $authorName = $bookItem->getAuthor()->getName();
                }

                $returnArr[] = [
                    "Id" => $bookItem->getId(),
                    "Name" => $bookItem->getTranslations()->getValues()[$translationContextKey]->getContext(),
                    "Author" => [
                        "Id" => $bookItem->getAuthor()->getId(),
                        "Name" => $authorName,
                    ]
                ];
            }
        }

        return $returnArr;
    }

    public static function mapGetBookResponse($_locale, Book $bookItem)
    {
        if ($_locale !== "en") {
            $translationContextKey = self::getLocaleKeyForTranslationsContext($_locale);
            $bookName = $bookItem->getTranslations()->getValues()[$translationContextKey]->getContext();
        } else {
            $bookName = $bookItem->getName();
        }

        $returnArr[] = [
            "Id" => $bookItem->getId(),
            "Name" => $bookName,
        ];
     
        return $returnArr[0];
    }


    
}
