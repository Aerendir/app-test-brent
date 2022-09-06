<?php

namespace App\Fetcher;

class FetcherRegistry
{

    public function __construct(private readonly iterable $fetchers) {}

    public function find(string $name):FetcherInterface
    {
        foreach ($this->fetchers as $fetcher) {
            if ($fetcher::getName() === $name) {
                return $fetcher;
            }
        }

        throw new \InvalidArgumentException(sprintf('Fetcher "%s" not found.', $name));
    }
}
