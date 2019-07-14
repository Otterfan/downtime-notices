<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\NotificationView;
use App\Entity\Template;
use App\Entity\User;
use App\Form\NotificationType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    /**
     * @Route("/notification", name="notification_list")
     */
    public function list(EntityManagerInterface $em, PaginatorInterface $paginator, Request $request)
    {
        $repo = $em->getRepository(Notification::class);
        $closed_query = $repo->findClosedQuery();
        $closed = $paginator->paginate($closed_query, $request->query->getInt('page', 1));

        $active = $repo->findActiveNotifications();
        $pending = $repo->findPending();

        return $this->render(
            'notification/list.html.twig',
            ['closed' => $closed, 'active' => $active, 'pending' => $pending]
        );
    }

    /**
     * @Route("/active", name="notification_active_list")
     */
    public function listActive(EntityManagerInterface $em, Request $request)
    {
        $constraints = [
            'type' => $request->query->get('type')
        ];
        $notes = $em->getRepository(Notification::class)->findActiveNotifications($constraints);
        return $this->buildJSONResponse($request, $notes);
    }

    /**
     * @Route("/pending", name="notification_pending_list")
     */
    public function listPending(EntityManagerInterface $em, Request $request)
    {
        $notes = $em->getRepository(Notification::class)->findPending();
        return $this->buildJSONResponse($request, $notes);
    }


    /**
     * @Route("/feed", name="notification_feed_list")
     */
    public function calendarFeed(EntityManagerInterface $em, Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');
        $notes = $em->getRepository(Notification::class)->findByStartDate($start, $end);

        $response = [];
        foreach ($notes as $note) {
            $base = $this->generateUrl('notification_list');
            $response[] = $note->calendarFeed($base);
        }

        return $this->json($response);
    }

    /**
     * @Route("/notification/new", name="notification_create")
     */
    public function new(EntityManagerInterface $em, Request $request): Response
    {
        $blank_note = new Notification();

        if ($request->get('template')) {
            $template = $em->find(Template::class, $request->get('template'));
            $blank_note->setText($template->getText());
            $blank_note->setPriority($template->getPriority());
            $blank_note->setType($template->getType());
            $blank_note->setApplication($template->getApplication());
        }

        $blank_note->activate();
        $form = $this->createForm(NotificationType::class, $blank_note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processForm($form, 'created');
        }

        return $this->renderEditForm($form, 'Create notification');
    }

    /**
     * @Route("/notification/search", name="notification_search", methods={"GET"})
     */
    public function search(EntityManagerInterface $em, PaginatorInterface $paginator, Request $request)
    {
        $term = $request->get('q', '');
        $query = $em->getRepository(Notification::class)->searchQuery($term);
        $notes = $paginator->paginate($query, $request->query->getInt('page', 1));
        return $this->render('notification/search.html.twig', ['notes' => $notes, 'query' => $term]);
    }

    /**
     * @Route("/notification/{id}", name="notification_edit", methods={"GET","POST"})
     */
    public function edit(EntityManagerInterface $em, string $id, Request $request)
    {
        $note = $em->find(Notification::class, $id);

        if (!$note) {
            return $this->redirectWithFlash('notification_create', "Could not find notification $id", 'warning');
        }

        $form = $this->createForm(NotificationType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processForm($form, 'edited');
        }

        return $this->renderEditForm($form, 'Edit notification', $templates);
    }

    /**
     * @Route("/notification/{id}/deactivate", name="notification_deactivate", methods={"PUT"})
     */
    public function deactivate(EntityManagerInterface $em, $id, Request $request): Response
    {
        $note = $em->find(Notification::class, $id);

        if (!$note) {
            return $this->redirectWithFlash('notification_list', "Could not find $uid", 'warning');
        }

        $note->deactivate();
        $this->saveNote($note, $em);

        return $this->redirectToHomeOrList($request, 'Deactivated notification');
    }

    /**
     * @Route("/notification/{id}/delete", name="notification_delete", methods={"PUT"})
     */
    public function delete(EntityManagerInterface $em, $id, Request $request): Response
    {
        $note = $em->find(Notification::class, $id);

        if (!$note) {
            return $this->redirectWithFlash('notification_list', "Could not find $uid", 'warning');
        }

        $em->remove($note);
        $em->flush();

        return $this->redirectToHomeOrList($request, 'Deleted notification');
    }

    /**
     * @Route("/notification/{id}/activate", name="notification_activate", methods={"PUT"})
     */
    public function activate(EntityManagerInterface $em, $id, Request $request): Response
    {
        $note = $em->find(Notification::class, $id);

        if (!$note) {
            return $this->redirectWithFlash('notification_list', 'Could not find notification', 'warning');
        }

        $note->activate();
        $this->saveNote($note, $em);

        return $this->redirectWithFlash('notification_list', 'Activated notification');
    }

    /**
     * @Route("/notification/{id}/reactivate", name="notification_reactivate", methods={"PUT"})
     */
    public function reactivate(EntityManagerInterface $em, $id, Request $request): Response
    {
        $old_note = $em->find(Notification::class, $id);

        if (!$old_note) {
            return $this->redirectWithFlash('notification_list', 'Could not find notification', 'warning');
        }

        $new_note = new Notification();
        $new_note->setText($old_note->getText());
        $new_note->setPoster($this->currentUser());

        $new_note->activate();
        $this->saveNote($new_note, $em);

        return $this->redirectWithFlash('notification_list', 'Reactivated notification');
    }

    /**
     * @Route("/notification/{id}/copy", name="notification_copy")
     */
    public function copy(EntityManagerInterface $em, $id, Request $request): Response
    {
        $old_note = $em->find(Notification::class, $id);

        if (!$old_note) {
            return $this->redirectWithFlash('notification_create', "Could not find notification $id", 'warning');
        }

        $form = $this->createForm(NotificationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processForm($form, 'copied');
        }

        $form->get('text')->setData($old_note->getText());
        $form->get('start')->setData(new \DateTime('now', new \DateTimeZone('America/New_York')));

        return $this->renderEditForm($form, 'Create notification');
    }

    private function processForm(FormInterface $form, string $success_verb): RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();

        $note = $form->getData();
        $note->setPoster($this->currentUser());
        $this->saveNote($note, $em);

        $message = "Notification $success_verb";
        return $this->redirectWithFlash('notification_list', $message);
    }

    /**
     * @param string $route
     * @param string $message
     * @param string $status
     * @return RedirectResponse
     */
    private function redirectWithFlash(string $route, string $message, string $status = 'success'): RedirectResponse
    {
        $this->addFlash($status, $message);
        return $this->redirectToRoute($route);
    }

    /**
     * @return User|object|null
     */
    private function currentUser()
    {
        $user = $this->getDoctrine()->getRepository(User::class)
            ->findOneBy(['uid' => 'florinb']);
        return $user;
    }

    /**
     * @param FormInterface $form
     * @param string $title
     * @return Response
     */
    private function renderEditForm(FormInterface $form, string $title = 'Edit notification'): Response
    {
        $templates = $this->getDoctrine()->getRepository(Template::class)->findAll();
        return $this->render(
            'notification/new.html.twig',
            [
                'form' => $form->createView(),
                'title' => $title,
                'templates' => $templates
            ]
        );
    }

    /**
     * @param Notification $new_note
     * @param EntityManagerInterface $em
     */
    private function saveNote(Notification $new_note, EntityManagerInterface $em): void
    {
        $em->persist($new_note);
        $em->flush();
    }

    /**
     * @param Request $request
     * @param string $message
     * @return RedirectResponse
     */
    protected function redirectToHomeOrList(Request $request, string $message): RedirectResponse
    {
        $url_parts = parse_url($request->headers->get('Referer'));
        if ($url_parts && strpos($url_parts['path'], 'dashboard')) {
            return $this->redirectWithFlash('home', $message);
        } else {
            return $this->redirectWithFlash('notification_list', $message);
        }
    }

    /**
     * @param Request $request
     * @param $notes
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    private function buildJSONResponse(Request $request, $notes): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $payload = [
            'datetime' => new \DateTime('now', new \DateTimeZone('America/New_York ')),
            'notes' => []
        ];

        $notes = $this->filterNotifications($request, $notes);

        foreach ($notes as $note) {
            $payload['notes'][] = $note->publicView();
        }

        $response = $this->json($payload);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    /**
     * @param Request $request
     * @param $notes
     * @return array
     */
    private function filterNotifications(Request $request, $notes): array
    {
        // @todo move all this to repository
        if ($app = $request->query->get('application')) {
            $notes = array_filter($notes, function (Notification $note) use ($app) {
                $normalized_name = strtolower($note->getApplication()->getName());
                return $normalized_name === strtolower($app);
            });
        }

        if ($type = $request->query->get('type')) {
            $notes = array_filter($notes, function (Notification $note) use ($type) {
                return $note->getType()->getName() === $type;
            });
        }

        if ($priority = $request->query->get('priority')) {
            $notes = array_filter($notes, function (Notification $note) use ($priority) {
                return $note->getPriority()->getLevel() <= (integer)$priority;
            });
        }
        return $notes;
    }
}