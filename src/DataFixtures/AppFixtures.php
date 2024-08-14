<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\AdFactory;
use App\Factory\CommentFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use function Zenstruck\Foundry\Persistence\flush_after;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        flush_after($this->createUsers(...));
        flush_after($this->createAds(...));
        flush_after($this->createComments(...));
    }

    private function createUsers(): void
    {
        UserFactory::createSequence([
            ['email' => 'user@example.com', 'name' => 'Paul Cook'],
            ['email' => 'hacker@example.com', 'name' => 'Kate Libby'],
            ['email' => 'admin@example.com', 'name' => 'Dade Murphy', 'roles' => [User::ROLE_ADMIN]],
        ]);
    }

    private function createAds(): void
    {
        $users = UserFactory::all();
        AdFactory::createMany(100, fn () => ['user' => $users[array_rand($users)]]);
    }

    private function createComments(): void
    {
        $users = UserFactory::all();
        $ads = AdFactory::all();
        CommentFactory::createMany(
            500,
            fn () => ['user' => $users[array_rand($users)], 'ad' => $ads[array_rand($ads)]]);
    }
}
