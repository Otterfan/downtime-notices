<?php

namespace App\Controller;

use App\Entity\BestBet;
use App\Form\BestBetType;
use App\Repository\BestBetRepository;
use App\Services\BestBetService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BestBetController extends AbstractController
{
    /**
     * @Route("/best-bet", name="best_bet_list")
     */
    public function list(BestBetRepository $repo, Request $request, PaginatorInterface $paginator)
    {
        $query = $repo->findAllQuery();
        $all = $paginator->paginate($query, $request->query->getInt('page', 1));

        return $this->render('best_bet/list.html.twig', [
            'betlist' => $all
        ]);
    }

    /**
     * @Route("/best-bet/new", name="best_bet_create")
     */
    public function new(Request $request, BestBetService $best_bet_client)
    {
        $bet = new BestBet();

        $form = $this->createForm(BestBetType::class, $bet);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $redirect = $this->processForm($form, 'created');
            $best_bet_client->add($bet);
            return $redirect;
        }

        return $this->render('best_bet/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/best-bet/{id}", name="best_bet_edit", methods={"GET","POST"})
     */
    public function edit(EntityManagerInterface $em, string $id, Request $request, BestBetService $best_bet_client)
    {
        $bet = $em->find(BestBet::class, $id);

        if (!$bet) {
            return $this->redirectWithFlash('best_bet_create', "Could not find best bet $id", 'warning');
        }

        $form = $this->createForm(BestBetType::class, $bet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $redirect = $this->processForm($form, 'edited');
            $best_bet_client->update($bet);
            return $redirect;
        }

        return $this->render('best_bet/edit.html.twig', [
            'bet' => $bet,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/best-bet/{id}", name="best_bet_delete", methods={"DELETE"})
     */
    public function delete(Request $request, BestBet $bet, BestBetService $best_bet_client): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $bet->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            foreach ($bet->getTerms() as $term) {
                $entityManager->remove($term);
            }
            $entityManager->remove($bet);
            $entityManager->flush();

            $best_bet_client->delete($bet);
        }

        return $this->redirectToRoute('best_bet_list');
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
        return $this->redirectWithFlash('best_bet_list', $message);
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
