<?php

namespace App\Controller;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(EntityManagerInterface $em)
    {
        $repo = $em->getRepository(Notification::class);

        $notes = $repo->findActiveNotifications();

        return $this->render(
            'home/index.html.twig',
            [
                'notes'           => $notes,
                'controller_name' => 'HomeController',
            ]
        );
    }
}
