<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
    }

    public function testRegisterSucceeds(): void
    {
        $this->client->request('GET', '/register');
        self::assertResponseIsSuccessful();
        self::assertPageTitleContains('Register');

        $this->client->submitForm('Register', [
            'registration_form[email]' => 'newuser@example.com',
            'registration_form[plainPassword]' => 'Password123',
            'registration_form[name]' => 'Test User',
            'registration_form[phone]' => '+999999999',
            'registration_form[agreeTerms]' => true,
        ]);

        self::assertResponseRedirects('/');
        self::assertCount(4, $this->userRepository->findAll());
        $user = $this->userRepository->findOneBy(['email' => 'newuser@example.com']);
        self::assertTrue($user instanceof User);
        self::assertSame('Test User', $user->getName());
        self::assertSame('+999999999', $user->getPhone());
        self::assertTrue(password_verify('Password123', (string) $user->getPassword()));
    }
}
