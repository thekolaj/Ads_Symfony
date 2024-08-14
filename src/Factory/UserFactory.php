<?php

namespace App\Factory;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
    ) {
        parent::__construct();
    }

    public static function class(): string
    {
        return User::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'email' => self::faker()->unique()->safeEmail(),
            'name' => self::faker()->name(),
            'phone' => self::faker()->e164PhoneNumber(),
            'password' => 'Password123',
            'roles' => [User::ROLE_USER],
        ];
    }

    protected function initialize(): static
    {
        return $this->afterInstantiate(function (User $user) {
            $user->setPassword($this->hasher->hashPassword($user, $user->getPassword()));
        });
    }
}
