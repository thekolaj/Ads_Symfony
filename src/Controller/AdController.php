<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\User;
use App\Form\AdType;
use App\Repository\AdRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/ad', name: 'ad_')]
class AdController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(Request $request, AdRepository $adRepository): Response
    {
        return $this->render('ad/index.html.twig', [
            'pager' => $adRepository->listOrderedPaginated((int) $request->query->get('page', '1')),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, #[CurrentUser] User $user, EntityManagerInterface $entityManager): Response
    {
        $ad = new Ad();
        $ad->setUser($user);
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ad);
            $entityManager->flush();

            return $this->redirectToRoute('ad_show', ['id' => $ad->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ad/new.html.twig', [
            'ad' => $ad,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Request $request, int $id, AdRepository $adRepository, CommentRepository $commentRepository): Response
    {
        $ad = $adRepository->findOneById($id);
        $comments = $commentRepository->listByAdOrderedPaginated($ad, (int) $request->query->get('page', '1'));

        return $this->render('ad/show.html.twig', [
            'ad' => $ad,
            'comments' => $comments,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    #[IsGranted('CAN_UPDATE', 'ad')]
    public function edit(Request $request, Ad $ad, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('ad_show', ['id' => $ad->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ad/edit.html.twig', [
            'ad' => $ad,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    #[IsGranted('CAN_UPDATE', 'ad')]
    public function delete(Request $request, Ad $ad, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ad->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($ad);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ad_index', [], Response::HTTP_SEE_OTHER);
    }
}
