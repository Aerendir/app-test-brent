<?php

namespace App\Fetcher\Fetcher;

use App\Entity\Price;
use App\Fetcher\FetcherInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DataHubFetcher implements FetcherInterface
{
    private const ENDPOINT = 'https://datahub.io/core/oil-prices/r/brent-daily.json';

    private readonly LoggerInterface $logger;

    public function __construct(LoggerInterface $logger, private readonly HttpClientInterface $client)
    {
        if ($logger instanceof Logger) {
            $logger = $logger->withName(self::getName());
        }

        $this->logger = $logger;
    }

    public static function getName():string
    {
        return 'DataHub.io';
    }

    /**
     * @return array<Price>
     */
    public function fetch() : array
    {
        $this->logger->info('Fetching prices of BRENT...');
        $data = $this->client->request('GET', self::ENDPOINT)->toArray();
        $this->logger->debug('Prices of BRENT fetched');

        $this->logger->info('Processing prices of BRENT...');
        $prices = array_map(fn($price) => new Price($price['Date'], $price['price']), $data);
        $this->logger->debug('Prices of BRENT processed');

        return $prices;
    }
}
