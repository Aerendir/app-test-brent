<?php

namespace App\Controller\Api\Brent;

use App\Controller\Api\AbstractApiController;
use App\Controller\Api\Brent\Input\BrentInput;
use App\Exception\ApiInvalidRequestData;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation as NOA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class BrentController extends AbstractApiController
{
    /**
     * Returns the price points of Brent in the specified time period.
     *
     * @OA\RequestBody(@NOA\Model(type=BrentInput::class))
     */
    #[Route('/brent', methods: ['POST'])]
    public function get(Request $request): Response
    {
        $form = $this->createForm(BrentInput::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if (false === $form->isValid()) {
            throw new ApiInvalidRequestData($form);
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }
}
