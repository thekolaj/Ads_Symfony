<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private ObjectManager $manager;

    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $users = $this->loadUsers();

        $this->manager->flush();
    }

    /**
     * @return User[]
     */
    private function loadUsers(): array
    {
        $firstUser = new User();
        $firstUser
            ->setEmail('user@example.com')
            ->setName('Paul Cook')
            ->setPhone('+123456789')
            ->setPassword($this->hasher->hashPassword($firstUser, 'Password123'))
            ->setRoles([User::ROLE_USER]);
        $this->manager->persist($firstUser);

        $secondUser = new User();
        $secondUser
            ->setEmail('hacker@example.com')
            ->setName('Kate Libby')
            ->setPhone('+888888888')
            ->setPassword($this->hasher->hashPassword($secondUser, 'Password123'))
            ->setRoles([User::ROLE_USER]);
        $this->manager->persist($secondUser);

        $adminUser = new User();
        $adminUser
            ->setEmail('admin@example.com')
            ->setName('Dade Murphy')
            ->setPhone('+99999999')
            ->setPassword($this->hasher->hashPassword($adminUser, 'Password123'))
            ->setRoles([User::ROLE_ADMIN]);
        $this->manager->persist($adminUser);

        return [$firstUser, $secondUser, $adminUser];
    }
}
