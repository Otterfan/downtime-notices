<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LuckyController extends AbstractController
{
    /**
     * @param LoggerInterface $logger
     * @return Response
     * @throws \Exception
     * @Route("/lucky/number")
     */
    public function number(LoggerInterface $logger)
    {
        $number = random_int(0, 100);

        $logger->info('We are logging!');

        return $this->render(
            'lucky/number.html.twig',
            [
                'number' => $number,
            ]
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     * @Route("/lucky/json-number")
     */
    public function jsonNumber()
    {
        $number = random_int(0, 100);
        return $this->json(['number' => $number]);
    }
}