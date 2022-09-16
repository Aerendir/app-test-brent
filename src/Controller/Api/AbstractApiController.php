<?php

namespace App\Controller\Api;

use App\Serializer\ApiGroups;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

abstract class AbstractApiController extends AbstractController
{
    protected function apiResponse($data, $groups = []) : Response
    {
        $defaultGroup = [ApiGroups::ALWAYS];
        $data = [
            'jsonrpc' => '2.0',
            'id' => 1,
            'result' => $data,
        ];

        return $this->json($data, context: ['groups' => array_merge($defaultGroup, $groups), DateTimeNormalizer::FORMAT_KEY => 'Y-m-d']);
    }
}
