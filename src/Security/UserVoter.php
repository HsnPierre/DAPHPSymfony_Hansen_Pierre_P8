<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class UserVoter extends Voter
{
    const EDIT = 'user_edit';
    private $security;

    
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject): bool
    {
        if (!in_array($attribute, [self::EDIT])) {
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

        /** @var User $task */
        $user = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    public function canEdit(User $user): bool
    {
        if($this->security->isGranted('ROLE_ADMIN')){
            return true;
        }
        return false;
    }
}