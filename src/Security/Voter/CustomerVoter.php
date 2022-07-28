<?php

namespace App\Security\Voter;

use App\Entity\Customer;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CustomerVoter extends Voter
{
    public const ADD = 'ADD';
    public const REMOVE = 'REMOVE';
    public const SEE = 'SEE';

    protected function supports(string $attribute, $subject): bool
    {
        // remplacer avec votre propre logique
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::ADD, self::REMOVE, self::SEE])
            && $subject instanceof \App\Entity\Customer;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    { 
        $user = $token->getUser();

        // Si l'utilisateur est anonyme, on ne lui donne pas la permission
        if (!$user instanceof UserInterface) {
            return false;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // ... (check les conditions et retourne true si la permission est donnée) ...
        switch ($attribute) {
            case self::ADD || self::REMOVE || self::SEE:
                // logique qui determine si l'utilisateur peut ADD
                return $user === $subject;
                break;
        }

        return false;
    }
}
