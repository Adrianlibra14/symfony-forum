<?php

namespace App\Security\Voter;

use App\Entity\Post;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class PostVoter extends Voter
{
    const DELETE = 'delete';
    const EDIT = 'edit';

    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::DELETE, self::EDIT])
            && $subject instanceof \App\Entity\Post;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Subject is a Post object
        $post = $subject;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::DELETE:
                // logic to determine if the user can DELETE
                // return true or false
                return $this->canDelete($post, $user);
            case self::EDIT:
                // logic to determine if the user can EDIT
                return $this->canEdit($post, $user);
        }

        return false;
    }

    private function canDelete(Post $post, UserInterface $user) : bool
    {
        return $post->getUser()->getId() === $user->getId();  
    }

    private function canEdit(Post $post, UserInterface $user)
    {
        // If they can delete, they can edit
        return $this->canDelete($post, $user);
    }
}
