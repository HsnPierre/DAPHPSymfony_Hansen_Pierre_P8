<?php

namespace App\Security;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class TaskVoter extends Voter
{
    const EDIT = 'task_edit';
    const CREATE = 'task_create';
    const DELETE = 'task_delete';
    private $security;

    
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject): bool
    {
        if (!in_array($attribute, [self::CREATE, self::EDIT,self::DELETE])) {
            return false;
        }

        if (!$subject instanceof Task) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Task $task */
        $task = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEditorDelete($task, $user);
            case self::CREATE:
                return $this->canCreate($task, $user);
            case self::DELETE:
                return $this->canEditorDelete($task, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    public function canEditorDelete(Task $task, User $user): bool
    {
        if($user === $task->getAuthor() || $this->security->isGranted('ROLE_ADMIN')){
            return true;
        }
        return false;
    }

    public function canCreate(Task $task, User $user): bool
    {
        if($this->security->isGranted('ROLE_USER')){
            return true;
        }
        return false;
    }
}