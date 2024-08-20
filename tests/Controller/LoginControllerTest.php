<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testLoginSucceeds(): void
    {
        $this->client->request('GET', '/login');
        self::assertResponseIsSuccessful();

        $this->client->submitForm('Log In', [
            '_username' => 'user@example.com',
            '_password' => 'Password123',
        ]);

        self::assertResponseRedirects('/');
        $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertSelectorNotExists('.alert-danger');
    }

    public function testLoginFailsForInvalidUser(): void
    {
        $this->client->request('GET', '/login');
        self::assertResponseIsSuccessful();

        $this->client->submitForm('Log In', [
            '_username' => 'doesNotExist@example.com',
            '_password' => 'password',
        ]);

        self::assertResponseRedirects('/login');
        $this->client->followRedirect();
        self::assertSelectorTextContains('.alert-danger', 'Invalid credentials.');
    }

    public function testLoginFailsForInvalidPassword(): void
    {
        $this->client->request('GET', '/login');
        self::assertResponseIsSuccessful();

        $this->client->submitForm('Log In', [
            '_username' => 'user@example.com',
            '_password' => 'bad-password',
        ]);

        self::assertResponseRedirects('/login');
        $this->client->followRedirect();
        self::assertSelectorTextContains('.alert-danger', 'Invalid credentials.');
    }

    public function testLogout(): void
    {
        $this->client->request('GET', '/login');
        self::assertResponseIsSuccessful();

        $this->client->submitForm('Log In', [
            '_username' => 'user@example.com',
            '_password' => 'Password123',
        ]);

        $this->client->request('GET', '/ad/new');
        self::assertResponseIsSuccessful();
        self::assertPageTitleContains('New Ad');

        $this->client->request('GET', '/logout');
        self::assertResponseRedirects('/');

        $this->client->request('GET', '/ad/new');
        self::assertResponseRedirects('/login');
    }
}
