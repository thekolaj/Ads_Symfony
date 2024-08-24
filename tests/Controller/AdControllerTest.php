<?php

namespace App\Tests\Controller;

use App\Entity\Ad;
use App\Entity\User;
use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private AdRepository $adRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->adRepository = static::getContainer()->get(AdRepository::class);
        $this->userRepository = static::getContainer()->get(UserRepository::class);
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertPageTitleContains('Ads');
        $this->assertCount(20, $crawler->filter('tbody > tr'));
    }

    public function testNew(): void
    {
        $adCount = $this->adRepository->count();
        $user = $this->userRepository->findOneBy(['email' => 'user@example.com']);
        self::assertTrue($user instanceof User);
        $this->client->loginUser($user);
        $this->client->request('GET', '/ad/new');

        self::assertResponseIsSuccessful();

        $this->client->submitForm('Save', [
            'ad[title]' => 'Testing',
            'ad[description]' => 'Testing Testing Testing',
            'ad[price]' => '999',
        ]);

        self::assertSame($adCount + 1, $this->adRepository->count());
        $newAd = $this->adRepository->findOneBy(['title' => 'Testing']);
        self::assertTrue($newAd instanceof Ad);
        self::assertResponseRedirects('/ad/'.$newAd->getId());

        self::assertSame($user->getId(), $newAd->getUser()?->getId());
        self::assertSame('Testing Testing Testing', $newAd->getDescription());
        self::assertSame('999.00', $newAd->getPrice());
    }

    public function testShow(): void
    {
        $ad = $this->adRepository->findOneBy([]);
        self::assertTrue($ad instanceof Ad);
        $this->client->request('GET', '/ad/'.$ad->getId());

        self::assertResponseIsSuccessful();
        self::assertPageTitleContains('Ad');
        self::assertAnySelectorTextSame('td', (string) $ad->getTitle());
        self::assertAnySelectorTextSame('td', (string) $ad->getDescription());
        self::assertAnySelectorTextSame('td', (string) $ad->getPrice());
    }

    public function testEdit(): void
    {
        $ad = $this->adRepository->findOneBy([]);
        self::assertTrue($ad instanceof Ad);
        self::assertTrue($ad->getUser() instanceof User);
        $this->client->loginUser($ad->getUser());

        $this->client->request('GET', '/ad/'.$ad->getId().'/edit');
        $this->client->submitForm('Update', [
            'ad[title]' => 'New Title',
            'ad[description]' => 'New description, not tha same as last one!',
            'ad[price]' => '12345.67',
        ]);

        self::assertResponseRedirects('/ad/'.$ad->getId());
        $updatedAd = $this->adRepository->find($ad->getId());
        self::assertTrue($updatedAd instanceof Ad);
        self::assertSame('New Title', $updatedAd->getTitle());
        self::assertSame('New description, not tha same as last one!', $updatedAd->getDescription());
        self::assertSame('12345.67', $updatedAd->getPrice());
    }

    public function testRemove(): void
    {
        $adCount = $this->adRepository->count();
        $ad = $this->adRepository->findOneBy([]);
        self::assertTrue($ad instanceof Ad);
        self::assertTrue($ad->getUser() instanceof User);
        $this->client->loginUser($ad->getUser());

        $this->client->request('GET', '/ad/'.$ad->getId());
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/ad/');
        self::assertSame($adCount - 1, $this->adRepository->count());
        self::assertNull($this->adRepository->find($ad->getId()));
    }
}
