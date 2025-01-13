<?php

namespace App\Command;

use App\Repository\BestBetRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'bestbets:check',
    description: 'Check best bet sources for updates',
)]
class BestbetsCheckCommand extends Command
{
    private BestBetRepository $repository;

    public function __construct(BestBetRepository $repository)
    {
        $this->repository = $repository;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $best_bets = $this->repository->findAll();

        foreach ($best_bets as $best_bet) {
            $date = $best_bet->getUpdated();

            // Skip 'other' source type.
            if ($best_bet->getSourceType() === 'other') {
                continue;
            } elseif ($best_bet->getSourceType() === 'azlist') {
                // @todo implment A-Z list checks
                continue;
            } elseif ($best_bet->getSourceType() === 'faq') {
                // @todo implment FAQ checks
                continue;
            }

            $io->writeln("Checking {$best_bet->getTitle()}");
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
