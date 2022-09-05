<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: FetchPricesCommand::NAME,
    description: 'Fetch daily price of crude oil on brent from DataHub.io.',
)]
class FetchPricesCommand extends Command
{
    public const NAME = 'app:fetch-prices';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title(self::NAME);

        return Command::SUCCESS;
    }
}
