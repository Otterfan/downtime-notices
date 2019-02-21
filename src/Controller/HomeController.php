<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Notification;
use App\UptimeRobot\Client;
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

        $monitors = $this->queryUptimeRobot($em);

        return $this->render(
            'home/index.html.twig',
            [
                'monitors'        => $monitors,
                'notes'           => $notes,
                'controller_name' => 'HomeController',
            ]
        );
    }

    /**
     * @param EntityManagerInterface $em
     * @return \App\UptimeRobot\Monitor[]|array
     */
    private function queryUptimeRobot(EntityManagerInterface $em)
    {
        $apps = $em->getRepository(Application::class)->findAll();

        $apps_to_check = [];
        foreach ($apps as $app) {
            $apps_to_check[] = $app->getUptimeRobotCode();
        }

        $api_key = getenv('UPTIME_ROBOT_API_KEY');

        $ur_client = new Client();
        return $ur_client->fetch($api_key, $apps_to_check);
    }
}
