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

namespace App\Security;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

final class EmailVerifier
{
    public function __construct(
        private readonly VerifyEmailHelperInterface $verifyEmailHelper,
        private readonly MailerInterface $mailer,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function sendEmailConfirmation(
        string $verifyEmailRouteName,
        UserInterface $user,
        TemplatedEmail $templatedEmail
    ): void {
        $verifyEmailSignatureComponents = $this->verifyEmailHelper->generateSignature(
            $verifyEmailRouteName,
            $user->getUserIdentifier(),
            $user->email,
            ['id' => $user->id]
        );

        $context = $templatedEmail->getContext();
        $context['signedUrl'] = $verifyEmailSignatureComponents->getSignedUrl();
        $context['expiresAtMessageKey'] = $verifyEmailSignatureComponents->getExpirationMessageKey();
        $context['expiresAtMessageData'] = $verifyEmailSignatureComponents->getExpirationMessageData();

        $templatedEmail->context($context);

        $this->mailer->send($templatedEmail);
    }

    /**
     * @throws VerifyEmailExceptionInterface
     */
    public function handleEmailConfirmation(Request $request, UserInterface $user): void
    {
        $url = $request->getUri();
        $userId = $user->getUserIdentifier();
        $this->verifyEmailHelper->validateEmailConfirmationFromRequest($request, $userId, $user->email);

        $user->isVerified = true;

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
