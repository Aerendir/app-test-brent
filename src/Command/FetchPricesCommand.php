<?php

namespace App\Command;

use App\Fetcher\Fetcher\DataHubFetcher;
use App\Fetcher\FetcherRegistry;
use App\Manager\PricesManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[AsCommand(
    name: FetchPricesCommand::NAME,
    description: 'Fetch daily price of crude oil on brent from DataHub.io.',
)]
class FetchPricesCommand extends Command
{
    public const NAME = 'app:fetch-prices';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FetcherRegistry $fetcherRegistry,
        private readonly PricesManager $pricesManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title(self::NAME);

        $fetcher = $this->fetcherRegistry->find(DataHubFetcher::getName());
        try {
            $prices = $fetcher->fetch();
        } catch (TransportExceptionInterface $exception) {
            $io->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->pricesManager->persistPrices($prices);

        $io->writeln('');
        $io->writeln('Saving prices in the DB');
        $this->entityManager->flush();

        $io->success('All prices fetched and saved in the DB');

        return Command::SUCCESS;
    }
}
