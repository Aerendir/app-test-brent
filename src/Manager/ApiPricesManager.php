<?php

namespace App\Manager;

use App\Controller\Api\_Common\Input\DateIso8601RangeApiInput;
use App\Repository\PriceRepository;

class ApiPricesManager
{
    public function __construct(private readonly PriceRepository $priceRepository) {}

    public function getOilPriceTrend(array $params):array
    {
        return [
            'prices' => $this->priceRepository->findByDateRange($params[DateIso8601RangeApiInput::START_DATE], $params[DateIso8601RangeApiInput::END_DATE])->toArray()
        ];
    }
}
