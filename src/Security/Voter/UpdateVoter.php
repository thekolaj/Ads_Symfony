<?php

namespace App\Security\Voter;

use App\Entity\Interface\HasUserInterface;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<'CAN_UPDATE', HasUserInterface>
 */
class UpdateVoter extends Voter
{
    public const UPDATE = 'CAN_UPDATE';

    public function __construct(
        private readonly Security $security,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::UPDATE === $attribute && $subject instanceof HasUserInterface;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($this->security->isGranted(User::ROLE_ADMIN)) {
            return true;
        }

        if (!$subject instanceof HasUserInterface) {
            throw new \InvalidArgumentException('Subject must be instance of HasUserInterface');
        }

        return $user === $subject->getUser();
    }
}
