<?php

namespace App\Controller;

use App\Service\ValidationCheck;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthorController extends AbstractController
{
    /**
     * @Route(
     * "/author/create",
     * name="app_create_author",
     * methods={"PUT"})
     */
    public function createAuthorAction(Request $request, ValidatorInterface $validator, EntityManagerInterface $_em, ValidationCheck $validationCheck): JsonResponse
    {
        $requestJSON = $request->getContent();
        $serializer = $this->container->get('serializer');

        try {
            $newAuthor = $serializer->deserialize($requestJSON, 'App\Entity\Author', 'json', ['groups' => 'create_author']);
        } catch (Exception $e) {
            return new JsonResponse(null, Response::HTTP_UNPROCESSABLE_ENTITY);            
        }

        $validation = $validator->validate($newAuthor);
        $validationCheckResult = $validationCheck->formatValidationErrors($validation);

        if ($validationCheckResult) {
            return new JsonResponse($validationCheckResult, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $_em->persist($newAuthor);
        $_em->flush();

        return new JsonResponse(null, Response::HTTP_OK);
    }
}
