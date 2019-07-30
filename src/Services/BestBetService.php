<?php

namespace App\Services;

use App\Entity\BestBet;
use App\Entity\BestBetTerm;
use App\Repository\BestBetRepository;
use Elasticsearch\Client;

class BestBetService
{
    /**
     * @var Client
     */
    private $elastic;

    /**
     * @var BestBetRepository
     */
    private $repo;

    /**
     * @var \Parsedown
     */
    private $md;

    public function __construct(Client $elastic, BestBetRepository $repo, \Parsedown $md)
    {
        $this->elastic = $elastic;
        $this->repo = $repo;
        $this->md = $md;
        $this->md->setSafeMode(true);
    }

    public function add(BestBet $bet)
    {
        $terms = [];
        foreach ($bet->getTerms() as $term) {
            $terms[] = $term->getValue();
        }

        $params = [
            'index' => 'bestbets',
            'type' => 'bestbet',
            'id' => $bet->getId(),
            'body' => [
                'title' => $bet->getTitle(),
                'link' => $bet->getLink(),
                'displayText' => $this->md->line($bet->getText()),
                'terms' => $terms
            ]
        ];
        $this->elastic->index($params);
    }

    public function update(BestBet $bet)
    {
        $this->add($bet);
    }

    public function delete(BestBet $bet)
    {
        $params = [
            'index' => 'bestbets',
            'type' => 'bestbet',
            'id' => $bet->getId()
        ];

        $this->elastic->delete($params);
    }

    public function reset()
    {
        //@todo Add reset
    }
}