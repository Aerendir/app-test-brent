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
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[AsCommand(
    name: FetchPricesCommand::NAME,
    description: 'Fetch daily price of crude oil on brent from DataHub.io.',
)]
class FetchPricesCommand extends Command
{
    public const NAME = 'app:fetch-prices';
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

        $sourceName = $input->getOption(self::OPT_SOURCE);

        if (null === $sourceName) {
            $helper = $this->getHelper('question');
            $question = new ChoiceQuestion(
                'Please, select the source',
                $this->fetcherRegistry->getNamesOfAvailableFetchers(),
            );

            $question->setErrorMessage('The source is invalid');
            $sourceName = $helper->ask($input, $output, $question);
        }

        $fetcher = $this->fetcherRegistry->findByName($sourceName);
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
