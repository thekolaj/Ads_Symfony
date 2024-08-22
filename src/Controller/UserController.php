<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\AdRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    #[Route('/profile', name: 'profile', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED')]
    public function show(#[CurrentUser] User $user, AdRepository $adRepository): Response
    {
        return $this->render('user/show.html.twig', [
            'ads' => $adRepository->findList($user),
        ]);
    }
}
