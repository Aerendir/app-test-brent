<?php

namespace App\Fetcher;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

interface FetcherInterface
{
    public function __construct(LoggerInterface $logger, HttpClientInterface $client);
    public static function getName():string;
    public function fetch():array;
}
