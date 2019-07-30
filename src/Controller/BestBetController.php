<?php

namespace App\Controller;

use App\Entity\BestBet;
use App\Form\BestBetType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BestBetController extends AbstractController
{
    /**
     * @Route("/best-bet", name="best_bet")
     */
    public function index()
    {
        return $this->render('best_bet/index.html.twig', [
            'controller_name' => 'BestBetController',
        ]);
    }

    /**
     * @Route("/best-bet/new", name="best_bet_create")
     */
    public function new(Request $request)
    {
        $bet = new BestBet();

        $form = $this->createForm(BestBetType::class, $bet);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processForm($form, 'created');
        }

        return $this->render('best_bet/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function processForm(FormInterface $form, string $success_verb): RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();

        /**
         * @var BestBet $bet
         */
        $bet = $form->getData();
        $em->persist($bet);
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
}
