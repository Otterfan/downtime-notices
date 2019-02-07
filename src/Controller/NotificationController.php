<?php

namespace App\Controller;

use App\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    /**
     * @Route("/notification", name="notification")
     */
    public function index()
    {
        return $this->render(
            'notification/index.html.twig',
            [
                'controller_name' => 'NotificationController',
            ]
        );
    }

    /**
     * @return Response
     * @Route("/populate", name="populate_notifications")
     * @throws \Exception
     */
    public function populate(): Response
    {
        $entity_manager=  $this->getDoctrine()->getManager();

        $note = new Notification();
        $note->setText('The first message');
        $note->setStart(new \DateTime());

        $entity_manager->persist($note);
        $entity_manager->flush();

        return $this->render(
            'notification/populate.html.twig',
            [
                'controller_name' => 'NotificationController',
            ]
        );
    }
}
