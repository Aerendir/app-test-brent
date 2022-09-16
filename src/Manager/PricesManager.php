<?php

namespace App\Manager;

use App\Entity\Price;
use App\Repository\PriceRepository;
use Doctrine\Common\Collections\Criteria;
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

    /**
     * @param array<Price> $prices
     */
    public function persistPrices(array $prices):void
    {
        $prices = $this->filterOutAlreadyPersistedPrices($prices);

        $totalPrices = \count($prices);
        $this->logger->info('Persisting {total_prices} prices of BRENT', ['total_prices' => $totalPrices]);

        $i = 0;
        foreach ($prices as $price) {
            $i++;
            // This may be improved with flushes of batches
            $this->persistPrice($price, $i, $totalPrices);
        }

        $this->logger->debug('All {total_prices} are persisted', ['total_prices' => $totalPrices]);
    }

    public function persistPrice(Price $price, int $i = null, int $totalPrices = null):void
    {
        // `false` when using the method outside a cycle
        is_int($i) && is_int($totalPrices)
            ? $this->logger->debug('[{current_price}/{total_prices}] [{price_date}] Persisting...', ['current_price' => $i, 'total_prices' => $totalPrices, 'price_date' => $price->getDate()->format('Y-m-d')])
            : $this->logger->debug('[{price_date}] Persisting...', ['price_date' => $price->getDate()->format('Y-m-d')]);

        $this->entityManager->persist($price);
    }

    private function filterOutAlreadyPersistedPrices(array $prices):array
    {
        $lastPrice = $this->findLastFetchedPrice();

        if (false === $lastPrice instanceof Price) {
            return $prices;
        }

        $this->logger->info('Filtering out prices of days before {date}...', ['date' => $lastPrice->getDate()->format('Y-m-d')]);
        $prices = array_filter($prices, static fn(Price $price) => $lastPrice->getDate() < $price->getDate());
        $this->logger->debug('Prices of BRENT filtered out');

        return $prices;
    }

    private function findLastFetchedPrice():?Price
    {
        /** @var PriceRepository $pricesRepo */
        $pricesRepo = $this->entityManager->getRepository(Price::class);

        return $pricesRepo->findOneBy([], ['date' => Criteria::DESC]);
    }
}
