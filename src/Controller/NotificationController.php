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
    public function list(PaginatorInterface $paginator, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Notification::class)->findAllQuery();

        $notifications = $paginator->paginate($query, $request->query->getInt('page', 1));
        return $this->render('notification/list.html.twig', ['notifications' => $notifications]);
    }

    /**
     * @Route("/notification/active", name="notification_active_list")
     */
    public function active(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $notes = $em->getRepository(Notification::class)->findActiveNotifications();

        $response = [
            'datetime' => new \DateTime('now', new \DateTimeZone('America/New_York '))
        ];

        $response['notes'] = array_map(
            function (Notification $note) use ($em) {
                $view = new NotificationView();
                $view->setNotification($note);
                $em->persist($view);
                return $note->publicView();
            },
            $notes
        );
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

        return $this->render(
            'notification/new.html.twig',
            [
                'note_form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/notification/{id}", name="notification_edit", methods={"GET","POST"})
     */
    public function edit(EntityManagerInterface $em, string $id, Request $request)
    {
        $notification = $em->find(Notification::class, $id);

        if (!$notification) {
            return $this->redirectWithFlash('notification_create', "Could not find notification $id", 'warning');
        }

        $form = $this->createForm(NotificationType::class, $notification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processForm($form, 'edited');
        }

        return $this->render('notification/edit.html.twig', ['note_form' => $form->createView()]);
    }

    /**
     * @Route("/notification/{id}/deactivate", name="notification_deactivate", methods={"PUT"})
     */
    public function deactivate(EntityManagerInterface $em, $id, Request $request): Response
    {
        $notification = $em->find(Notification::class, $id);

        if (!$notification) {
            return $this->redirectWithFlash('notification_list', "Could not find $uid", 'warning');
        }

        $notification->deactivate();
        $em->persist($notification);
        $em->flush();

        return $this->redirectWithFlash('notification_list', 'Deactivated notification');
    }

    /**
     * @Route("/notification/{id}/activate", name="notification_activate", methods={"PUT"})
     */
    public function activate(EntityManagerInterface $em, $id, Request $request): Response
    {
        $notification = $em->find(Notification::class, $id);

        if (!$notification) {
            return $this->redirectWithFlash('notification_list', 'Could not find notification', 'warning');
        }

        $notification->activate();
        $em->persist($notification);
        $em->flush();

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

        $em->persist($new_note);
        $em->flush();

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


        return $this->render('notification/edit.html.twig', ['note_form' => $form->createView()]);
    }

    private function processForm(FormInterface $form, string $success_verb): RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();

        $notification = $form->getData();
        $notification->setPoster($this->currentUser());
        $em->persist($notification);
        $em->flush();

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
}