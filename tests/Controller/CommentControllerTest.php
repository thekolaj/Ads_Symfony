<?php

namespace App\Tests\Controller;

use App\Entity\Ad;
use App\Entity\Comment;
use App\Entity\User;
use App\Repository\AdRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommentControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private AdRepository $adRepository;
    private UserRepository $userRepository;
    private CommentRepository $commentRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->adRepository = static::getContainer()->get(AdRepository::class);
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->commentRepository = static::getContainer()->get(CommentRepository::class);
    }

    public function testNew(): void
    {
        $ad = $this->adRepository->findOneBy([]);
        self::assertTrue($ad instanceof Ad);
        $commentCount = $this->commentRepository->count(['ad' => $ad]);
        $user = $this->userRepository->findOneBy(['email' => 'user@example.com']);
        self::assertTrue($user instanceof User);
        $this->client->loginUser($user);

        $this->client->request('GET', '/ad/'.$ad->getId().'/new-comment');
        self::assertResponseIsSuccessful();

        $this->client->submitForm('Save', ['comment[text]' => 'Testing']);

        self::assertResponseRedirects('/ad/'.$ad->getId());

        self::assertSame($commentCount + 1, $this->commentRepository->count(['ad' => $ad]));
        $newComment = $this->commentRepository->findOneBy(['text' => 'Testing']);
        self::assertInstanceOf(Comment::class, $newComment);
        self::assertSame($ad->getId(), $newComment->getAd()?->getId());
        self::assertSame($user->getId(), $newComment->getUser()?->getId());
    }

    public function testEdit(): void
    {
        $comment = $this->commentRepository->findOneBy([]);
        self::assertTrue($comment instanceof Comment);
        self::assertTrue($comment->getUser() instanceof User);
        self::assertTrue($comment->getAd() instanceof Ad);
        $this->client->loginUser($comment->getUser());

        $this->client->request('GET', '/comment/'.$comment->getId().'/edit');
        $this->client->submitForm('Update', ['comment[text]' => 'Something New']);

        self::assertResponseRedirects('/ad/'.$comment->getAd()->getId());

        $updatedComment = $this->commentRepository->find($comment->getId());
        self::assertInstanceOf(Comment::class, $updatedComment);
        self::assertSame('Something New', $updatedComment->getText());
    }

    public function testRemove(): void
    {
        $commentCount = $this->commentRepository->count();
        $comment = $this->commentRepository->findOneBy([]);
        self::assertTrue($comment instanceof Comment);
        self::assertTrue($comment->getUser() instanceof User);
        $this->client->loginUser($comment->getUser());

        $this->client->request('GET', '/comment/'.$comment->getId().'/edit');
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/ad/'.$comment->getAd()?->getId());
        self::assertSame($commentCount - 1, $this->commentRepository->count());
        self::assertNull($this->commentRepository->find($comment->getId()));
    }
}
