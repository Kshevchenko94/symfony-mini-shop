<?php

namespace App\Security\Voter;

use App\Entity\Order;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class OrderVoter extends Voter
{
    public const string EDIT = 'edit';

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute == self::EDIT
            && $subject instanceof Order;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token
    ): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::EDIT => $this->canEdit($subject, $user),
            default => false,
        };
    }

    /**
     * @param Order $order
     * @param UserInterface $user
     * @return bool
     */
    public function canEdit(Order $order, UserInterface $user): bool
    {
        return $user === $order->getUser();
    }
}
