<?php

namespace App\Service;

class ValidationCheck
{
    public static function formatValidationErrors($validationErrors)
    {
        if (false != count($validationErrors)) {
            foreach ($validationErrors as $violation) {
                $messages[$violation->getPropertyPath()][] = $violation->getMessage();
            }
            return ["errors" => $messages];
        }

        return false;
    }
}
