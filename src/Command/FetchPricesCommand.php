<?php

namespace App\Command;

use App\Fetcher\FetcherRegistry;
use App\Manager\PricesManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: FetchPricesCommand::NAME,
    description: 'Fetch daily price of crude oil on brent from DataHub.io.',
)]
class FetchPricesCommand extends Command
{
    public const NAME = 'app:fetch-prices';
    private const MAX_TIMES_TO_ASK = 2;
    private const OPT_SOURCE = 'source';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FetcherRegistry $fetcherRegistry,
        private readonly PricesManager $pricesManager
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            // I prefer Options over Arguments
            ->addOption(self::OPT_SOURCE, 's', InputOption::VALUE_REQUIRED, 'The name of the source from which fetch the prices.');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title(self::NAME);

        try {
            $sourceName = $this->getSourceName($input, $output);
            $prices = $this->fetchPrices($sourceName);
        } catch (\Throwable $throwable) {
            $io->error($throwable->getMessage());

            return self::FAILURE;
        }

        $this->pricesManager->persistPrices($prices);

        $io->newLine();
        $io->writeln('Saving prices in the DB');
        $this->entityManager->flush();

        $io->success('All prices fetched and saved in the DB');

        return Command::SUCCESS;
    }

    private function getSourceName(InputInterface $input, OutputInterface $output):string
    {
        return $input->getOption(self::OPT_SOURCE) ?? $this->askForSourceName($input, $output);
    }

    private function askForSourceName(InputInterface $input, OutputInterface $output):string
    {
        $helper = $this->getHelper('question');
        $availableFetchers = $this->fetcherRegistry->getNamesOfAvailableFetchers();

        $question = new ChoiceQuestion('Please, select the source', $availableFetchers);
        $question->setErrorMessage('The source is invalid');
        $question->setMaxAttempts(self::MAX_TIMES_TO_ASK);
        $question->setValidator(static fn ($value) => empty($value) || '' === trim($value) ? throw new \InvalidArgumentException('You must pass the name of the source.') : $value);

        return $helper->ask($input, $output, $question);
    }

    private function fetchPrices(string $sourceName):array
    {
        $fetcher = $this->fetcherRegistry->findByName($sourceName);

        return $fetcher->fetch();
    }
}
