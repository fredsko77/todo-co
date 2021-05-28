<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{

    const USER_EDIT = 'user_edit';

    const USER_MANAGE = 'user_manage';

    protected function supports($attribute, $user): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::USER_EDIT, self::USER_MANAGE])
        && $user instanceof User;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::USER_EDIT:
                // Vérifier si le user peut éditer le user
                return $this->canEdit($user, $subject);
                break;
            case self::USER_MANAGE:
                // Vérifier si le user peut accéder à la gestion des utilisateurs
                return $this->canManage($user);
                break;
        }

        return false;
    }

    /**
     * @param User|null $user
     * @param User $subject
     *
     * @return bool
     */
    private function canEdit(?User $user, User $subject): bool
    {
        return $subject === $user;
    }

    /**
     * @param User|null $user
     *
     * @return bool
     */
    private function canManage(?User $user): bool
    {
        return in_array('ROLE_ADMIN', $user->getRoles());
    }
}
