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

namespace App\Handler;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class RegistrationHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $userPasswordHasher,
        private EmailVerifier $emailVerifier
    ) {
    }

    /**
     * @param User   $user          The user to register
     * @param string $plainPassword The plain password
     */
    public function handleRegistration(User $user, string $plainPassword): void
    {
        $user->setPassword(
            $this->userPasswordHasher->hashPassword($user, $plainPassword)
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->sendVerificationEmail($user);
    }

    /**
     * @param Request        $request        The current request
     * @param UserRepository $userRepository The user repository
     */
    public function validateConditions(Request $request, UserRepository $userRepository): User
    {
        $id = $request->query->get('id');
        if (null === $id) {
            throw new NotFoundHttpException('User ID not provided.');
        }

        $user = $userRepository->find($id);
        if (null === $user) {
            throw new NotFoundHttpException('User not found.');
        }

        return $user;
    }

    private function sendVerificationEmail(User $user): void
    {
        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address('mailer@your-domain.com', 'Zen Server'))
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
    }
}
