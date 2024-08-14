<?php

namespace App\Factory;

use App\Entity\Ad;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Ad>
 */
final class AdFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Ad::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'title' => self::faker()->realTextBetween(20, 255),
            'description' => self::faker()->realTextBetween(50, 500),
            'price' => self::faker()->randomFloat(2, max: 9999),
            'user' => UserFactory::new(),
        ];
    }
}
