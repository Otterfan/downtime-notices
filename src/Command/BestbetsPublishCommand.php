<?php

namespace App\Command;

use App\Repository\BestBetRepository;
use App\Services\BestBetService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BestbetsPublishCommand extends Command
{
    protected static $defaultName = 'bestbets:publish';

    /**
     * @var BestBetService
     */
    private $service;

    /**
     * @var BestBetRepository
     */
    private $repository;

    public function __construct(BestBetService $service, BestBetRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Publish best bets to Elasticsearch');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $best_bets = $this->repository->findAll();

        foreach ($best_bets as $best_bet) {
            $io->writeln("Publishing {$best_bet->getTitle()}");
            $this->service->update($best_bet);
        }

        $io->success('Published to Elasticsearch');
    }
}
