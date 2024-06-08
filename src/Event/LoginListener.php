<?php

declare(strict_types=1);

/*
 * This file is part of the MediaEase project.
 *
 * (c) Thomas Chauveau <contact.tomc@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Event;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * LoginListener is used to update the session ID of the user in the database.
 */
final class LoginListener implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Updates the session ID of the user in the database.
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        if ($user instanceof User) {
            $session = $event->getRequest()->getSession();
            $user->setSessionId($session->getId());
            $this->entityManager->flush();
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'security.interactive_login' => 'onSecurityInteractiveLogin',
        ];
    }
}
