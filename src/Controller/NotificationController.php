<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\NotificationView;
use App\Entity\User;
use App\Form\NotificationType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
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
        $query = $em->getRepository(Notification::class)->findAllQuery();
        $notes = $paginator->paginate($query, $request->query->getInt('page', 1));
        return $this->render('notification/list.html.twig', ['notifications' => $notes]);
    }

    /**
     * @Route("/notification/active", name="notification_active_list")
     */
    public function active(EntityManagerInterface $em, Request $request)
    {
        $notes = $em->getRepository(Notification::class)->findActiveNotifications();

        $response = [
            'datetime' => new \DateTime('now', new \DateTimeZone('America/New_York ')),
            'notes'    => []
        ];

        foreach ($notes as $note) {
            $view = new NotificationView();
            $view->setNotification($note);
            $em->persist($view);
            $response['notes'][] = $note->publicView();
        }
        $em->flush();

        return $this->json($response);
    }

    /**
     * @Route("/notification/new", name="notification_create")
     */
    public function new(Request $request): Response
    {
        $blank_note = new Notification();
        $blank_note->activate();
        $form = $this->createForm(NotificationType::class, $blank_note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processForm($form, 'created');
        }

        return $this->renderEditForm($form, 'Create notification');
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

        return $this->renderEditForm($form, 'Edit notification');
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

        return $this->redirectWithFlash('notification_list', 'Deactivated notification');
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

        return $this->redirectWithFlash('notification_list', 'Deleted notification');
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
     * @param string        $title
     * @return Response
     */
    private function renderEditForm(FormInterface $form, string $title = 'Edit notification'): Response
    {
        return $this->render(
            'notification/form.html.twig',
            [
                'note_form' => $form->createView(),
                'title'     => $title
            ]
        );
    }

    /**
     * @param Notification           $new_note
     * @param EntityManagerInterface $em
     */
    private function saveNote(Notification $new_note, EntityManagerInterface $em): void
    {
        $em->persist($new_note);
        $em->flush();
    }
}