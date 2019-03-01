<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Notification;
use App\Entity\Template;
use App\UptimeRobot\Client;
use App\UptimeRobot\Monitor;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->redirectToRoute('dashboard');
    }


    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard(EntityManagerInterface $em, LoggerInterface $logger)
    {
        $repo = $em->getRepository(Notification::class);

        $notes = $repo->findActiveNotifications();

        $templates = $em->getRepository(Template::class)->findAll();

        $error = false;

        $apps = [];

        try {
            $apps = $this->queryUptimeRobot($em);
        } catch (\Exception $e) {
            $logger->error($e);
            $error = "Couldn't load Uptime Robot results.";
        }

        return $this->render(
            'home/index.html.twig',
            [
                'apps'            => $apps,
                'notes'           => $notes,
                'controller_name' => 'HomeController',
                'error_message'   => $error,
                'templates'       => $templates
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
            $apps_to_check[$app->getUptimeRobotCode()] = $app;
        }

        $api_key = getenv('UPTIME_ROBOT_API_KEY');

        $ur_client = new Client();
        $monitors = $ur_client->fetch($api_key, array_keys($apps_to_check));

        return array_map(
            function (Monitor $monitor) use ($apps_to_check) {
                return [
                    'monitor' => $monitor,
                    'app'     => $apps_to_check[$monitor->id]
                ];
            },
            $monitors
        );
    }
}
