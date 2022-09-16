<?php

namespace App\Exception;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ApiInvalidRequestData extends BadRequestHttpException
{
    public function __construct(protected FormInterface $form)
    {
        parent::__construct(message: "Invalid request", code: Response::HTTP_BAD_REQUEST);
    }

    public function getForm() : array
    {
        return [
            'errors' => $this->form
        ];
    }
}
