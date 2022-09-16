<?php

namespace App\Controller\Api\Brent;

use App\Controller\Api\AbstractApiController;
use App\Controller\Api\Brent\Input\BrentApiMethodsEnum;
use App\Controller\Api\Brent\Input\BrentInput;
use App\Exception\ApiInvalidRequestData;
use App\Manager\ApiPricesManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\RequestBody;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class BrentController extends AbstractApiController
{
    public function __construct(private readonly ApiPricesManager $apiPricesManager) {}

    /**
     * Returns the prices of Brent in the specified time period.
     */
    #[RequestBody(content: new JsonContent(ref: new Model(type: BrentInput::class)))]
    #[Route('/brent', methods: ['POST'])]
    public function get(Request $request): Response
    {
        $form = $this->createForm(BrentInput::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if (false === $form->isValid()) {
            throw new ApiInvalidRequestData($form);
        }

        $data = $form->getData();

        $prices = match ($data['method']) {
            BrentApiMethodsEnum::GetOilPriceTrend => $this->apiPricesManager->getOilPriceTrend($data['params']),
            // The method is actually already validated as all other fields,
            // but a default match is always a good idea.
            default => throw new \RuntimeException('This should never happen, but happened.'),
        };

        return $this->apiResponse($prices);
    }
}
