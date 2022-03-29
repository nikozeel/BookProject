<?php

namespace App\Controller;

use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Service\ValidationCheck;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookController extends AbstractController
{
    /**
     * @Route(
     *  "/book/search/{_locale}/{_search_query}",
     *  requirements={
     *      "_locale": "ru|en|de"
     *  }, 
     *  defaults={
     *      "_locale": "en"
     *  },
     *  name="app_search_book"),
     *  methods={"GET"}
     */
    public function searchBookAction(BookRepository $bookRepository, string $_locale, string $_search_query): JsonResponse
    {
        $returnArr = $bookRepository->findAllAuthorBooksByOneBookName($_locale, $_search_query);

        return new JsonResponse($returnArr, Response::HTTP_OK);
    }

    /**
     * @Route(
     *  "/{_locale}/book/{_id}",
     *  requirements={
     *      "_locale": "ru|en|de",
     *      "_id"="\d+"
     *  }, 
     *  defaults={
     *      "_locale": "en"
     *  },
     *  name="app_get_book"),
     *  methods={"GET"}
     */
    public function getBookAction(BookRepository $bookRepository, string $_locale, int $_id): JsonResponse
    {
        $returnArr = $bookRepository->getBookById($_locale, $_id);

        return new JsonResponse($returnArr, Response::HTTP_OK);
    }

    /**
     * @Route("/book/create",
     * name="app_create_book",
     * methods={"PUT"})
     */
    public function createBookAction(Request $request, ValidatorInterface $validator, EntityManagerInterface $_em, ValidationCheck $validationCheck, AuthorRepository $authorRepository): JsonResponse
    {
        $requestJSON = $request->getContent();
        $serializer = $this->container->get('serializer');
        
        try {
            $newBook = $serializer->deserialize($requestJSON, 'App\Entity\Book', 'json', ['groups' => 'create_book']);
            $authorId = $request->toArray()["author"]["id"];
            if (is_int($authorId)) {
                $author = $authorRepository->find($authorId);
                if ($author) {
                    $newBook->setAuthor($author);
                } else {
                    throw new Exception();
                }
            }
        } catch (Exception $e) {
            return new JsonResponse(null, Response::HTTP_UNPROCESSABLE_ENTITY);            
        }
        
        $validation = $validator->validate($newBook);
        $validationCheckResult = $validationCheck->formatValidationErrors($validation);

        if ($validationCheckResult) {
            return new JsonResponse($validationCheckResult, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $_em->persist($newBook);
        $_em->flush();

        return new JsonResponse(null, Response::HTTP_OK);
    }
}