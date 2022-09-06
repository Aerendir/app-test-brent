<?php

namespace App\Manager;

use App\Entity\Price;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class PricesManager
{
    private readonly LoggerInterface $logger;

    public function __construct(LoggerInterface $logger, private readonly EntityManagerInterface $entityManager)
    {
        if ($logger instanceof Logger) {
            $logger = $logger->withName('Prices');
        }

        $this->logger = $logger;
    }

    public function persistPrices(array $prices):void
    {
        $totalPrices = \count($prices);
        $this->logger->info('Persisting {total_prices}', ['total_prices' => $totalPrices]);

        $i = 0;
        foreach ($prices as $price) {
            $i++;
            $this->persistPrice($price, $i, $totalPrices);
        }

        $this->logger->debug('{total_prices} persisted', ['total_prices' => $totalPrices]);
    }

    public function persistPrice(Price $price, int $i = null, int $totalPrices = null):void
    {
        is_int($i) && is_int($totalPrices)
            ? $this->logger->debug('[{current_price}/{total_prices}] Persisting...', ['current_price' => $i, 'total_prices' => $totalPrices])
            : $this->logger->debug('Persisting...');

        $this->entityManager->persist($price);
    }
}
