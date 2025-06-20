<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Contracts\Validation\Validator;

class MyValidationException extends Exception
{
    protected $validator;

    protected $code = 200;

    protected $renderType;

    public function __construct(Validator $validator, $renderType)
    {
        $this->validator = $validator;
        $this->renderType = $renderType;
    }

    public function render()
    {
        // Get the validation errors
        $errors = $this->validator->errors();

        // Modify the error messages
        $modifiedErrors = $this->removeFieldNamesFromMessages($errors);

        return response()->json([
            "renderType" => 'validation',
            "status" => false,
            "message" => "form validation error",
            "data" => $modifiedErrors,
        ], $this->code);
    }


    private function removeFieldNamesFromMessages($errors)
    {
        $modified = [];

        foreach ($errors->messages() as $field => $messages) {
            foreach ($messages as $key => $message) {
                // Remove the field name part from the message
                $modified[$field][] = $this->stripFieldName($message);
            }
        }

        return $modified;
    }

    private function stripFieldName($message)
    {
        // Regular expression to remove the field name part
        $message = preg_replace('/^The .+ field /', 'This field ', $message);

        return $message;
    }
}
