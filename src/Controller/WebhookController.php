<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WebhookController extends AbstractController
{

    private const DOWN_ALERT_CODE = '1';
    private const UP_ALERT_CODE = '2';

    /**
     * @Route("/listen", name="webhook_listen")
     */
    public function listen(EntityManagerInterface $em, Request $req)
    {
        $id = $req->get('monitorID', false);
        $alert_type = $req->get('alertType', false);

        // Abort early if we don't have a good request.
        if (!$id || !$alert_type || !$this->alertTypeIsValid($alert_type)) {
            return $this->sendResponse();
        }

        $app = $em->getRepository(Application::class)->findByUptimeRobotId($id);

        // Abort early if no application.
        if (!$app) {
            return $this->sendResponse();
        }

        if ($alert_type = self::DOWN_ALERT_CODE) {
            $this->postNote($app, $em);
        } else {
            $this->removeNote($app, $em);
        }

        $em->flush();

        return $this->sendResponse();
    }

    protected function postNote(Application $application, EntityManagerInterface $em): void
    {
        $template = $application->getTemplate();

        if ($template) {
            $note = new Notification();
            $note->loadFromTemlpate($template);
            $note->setAutoposted(true);
            $note->activate();
            $em->persist($note);
        }
    }

    protected function removeNote(Application $app, EntityManagerInterface $em): void
    {
        $autoposts = $em->getRepository(Notification::class)->findActiveAutoposts();
        foreach ($autoposts as $autopost) {
            $posted_app = $autopost->getApplication();

            if ($posted_app && $posted_app->getId() === $app->getId()) {
                $autopost->deactivate();
            }
        }
    }

    /**
     * @return JsonResponse
     */
    protected function sendResponse(): JsonResponse
    {
        $response = $this->json(['status' => 'success']);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    protected function alertTypeIsValid(?string $alert_type): bool
    {
        $valid_alert_types = [self::DOWN_ALERT_CODE, self::UP_ALERT_CODE];
        return in_array($alert_type, $valid_alert_types, true);
    }
}