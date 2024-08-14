<?php

namespace App\Factory;

use App\Entity\Comment;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Comment>
 */
final class CommentFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Comment::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'text' => self::faker()->realTextBetween(5, 300),
            'ad' => AdFactory::new(),
            'user' => UserFactory::new(),
        ];
    }
}
