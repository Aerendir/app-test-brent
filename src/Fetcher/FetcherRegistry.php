<?php

namespace App\Fetcher;

class FetcherRegistry
{

    public function __construct(private readonly iterable $fetchers) {}

    public function findByName(string $name):FetcherInterface
    {
        foreach ($this->fetchers as $fetcher) {
            if ($fetcher::getName() === $name) {
                return $fetcher;
            }
        }

        throw new \InvalidArgumentException(sprintf('Fetcher "%s" not found.', $name));
    }

    public function getNamesOfAvailableFetchers():array
    {
        static $namesOfFetchers = null;

        if (false === is_array($namesOfFetchers)) {
            $namesOfFetchers = array_map(fn(FetcherInterface $fetcher) => $fetcher::getName(), array(...$this->fetchers));
        }

        return $namesOfFetchers;
    }
}
