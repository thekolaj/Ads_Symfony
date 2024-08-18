<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CommentController extends AbstractController
{
    #[Route('ad/{id}/new-comment', name: 'comment_new', methods: ['GET', 'POST'])]
    public function new(#[CurrentUser] User $user, Ad $ad, Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $comment->setUser($user);
        $comment->setAd($ad);
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('ad_show', ['id' => $ad->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/comment/{id}/edit', name: 'comment_edit', methods: ['GET', 'POST'])]
    #[IsGranted('CAN_UPDATE', 'comment')]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $adId = $comment->getAd()?->getId();

            return $this->redirectToRoute('ad_show', ['id' => $adId], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/comment/{id}', name: 'comment_delete', methods: ['POST'])]
    #[IsGranted('CAN_UPDATE', 'comment')]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $adId = $comment->getAd()?->getId();

        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ad_show', ['id' => $adId], Response::HTTP_SEE_OTHER);
    }
}
