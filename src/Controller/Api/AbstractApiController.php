<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractApiController extends AbstractController
{
    protected function apiResponse($data, $groups = []) : Response
    {
        $defaultGroup = ['always'];
        return $this->json($data, context: ['groups' => array_merge($defaultGroup, $groups)]);
    }
}
