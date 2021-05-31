<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskVoter extends Voter
{

    const TASK_EDIT = 'task_edit';

    const TASK_DELETE = 'task_delete';

    protected function supports($attribute, $task): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [
            self::TASK_EDIT,
            self::TASK_DELETE,
        ]) && $task instanceof Task;
    }

    protected function voteOnAttribute($attribute, $task, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::TASK_EDIT:
                // Vérifier si il le user peut éditer la tâche
                return $this->canEdit($user, $task);
                break;
            case self::TASK_DELETE:
                // Vérifier si il le user peut supprimer la tâche
                return $this->canDelete($user, $task);
                break;
        }

        return false;
    }

    /**
     * @param User|null $user
     * @param Task $task
     *
     * @return bool
     */
    private function canDelete(?User $user, Task $task): bool
    {
        return $user === $task->getUser() || (in_array('ROLE_ADMIN', $user->getRoles()) && $task->getUser() === null);
    }

    /**
     * @param User|null $user
     * @param Task $task
     *
     * @return bool
     */
    private function canEdit(?User $user, Task $task): bool
    {
        return $user === $task->getUser() || (in_array('ROLE_ADMIN', $user->getRoles()) && $task->getUser() === null);
    }
}
