<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user_list")
     */
    public function list(): Response
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository(User::class)->findAll();

        return $this->render(
            'user/list.html.twig',
            [
                'users' => $users
            ]
        );
    }

    /**
     * @Route("/user/new", name="user_create")
     */
    public function new(Request $request): Response
    {
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processForm($form, 'created');
        }

        return $this->render(
            'user/new.html.twig',
            [
                'user_form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/user/{uid}", name="user_edit", methods={"GET","POST"})
     */
    public function edit($uid, Request $request)
    {
        $user = $this->findUserByUid($uid);

        if (!$user) {
            return $this->redirectWithFlash('user_create', "Could not find user $uid", 'warning');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processForm($form, 'edited');
        }

        return $this->render('user/edit.html.twig', ['user_form' => $form->createView()]);
    }

    /**
     * @Route("/user/{uid}", name="user_delete", methods={"DELETE"})
     */
    public function delete(EntityManagerInterface $em, $uid, Request $request): Response
    {
        $user = $this->findUserByUid($uid);

        if (!$user) {
            return $this->redirectWithFlash('user_list', "Could not find $uid", 'warning');
        }

        $em->remove($user);
        $em->flush();

        return $this->redirectWithFlash('user_list', "Deleted $uid");
    }

    /**
     * @param FormInterface $form
     * @return RedirectResponse
     */
    private function processForm(FormInterface $form, string $success_verb): RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();

        $user = $form->getData();
        $em->persist($user);
        $em->flush();

        $message = "User {$user->getUid()} $success_verb";
        return $this->redirectWithFlash('user_list', $message);
    }

    /**
     * @param $uid
     * @return User
     */
    private function findUserByUid(string $uid): User
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['uid' => $uid]);
        return $user;
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

}
