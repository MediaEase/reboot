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

use App\Entity\Setting;
use App\Entity\User;
use App\Repository\SettingRepository;
use Psr\Log\LoggerInterface;
use App\Security\EmailVerifier;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use App\Service\CommandExecutorService;
use App\Service\PendingActivationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class RegistrationHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $userPasswordHasher,
        private EmailVerifier $emailVerifier,
        private CommandExecutorService $commandExecutorService,
        private LoggerInterface $logger,
        private PendingActivationService $pendingActivationService,
        private SettingRepository $settingRepository
    ) {
    }

    /**
     * Handle the registration process for a new user.
     *
     * @param User   $user          The user to register
     * @param string $plainPassword The plain password
     *
     * @throws \Exception
     */
    public function handleRegistration(User $user, string $plainPassword): void
    {
        $user->setPassword(
            $this->userPasswordHasher->hashPassword($user, $plainPassword)
        );
        $user->setRegisteredAt(new \DateTimeImmutable());

        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->pendingActivationService->create($user, $plainPassword);
            $setting = $this->settingRepository->findLast();
            if ($setting instanceof Setting && $setting->isEmailVerificationEnabled() === false) {
                $this->activateUser($user);
            } else {
                $this->sendVerificationEmail($user);
            }
        } catch (\Exception $exception) {
            $this->logger->error('User registration failed', [
                'exception' => $exception,
                'user' => $user->getUsername(),
            ]);
            throw $exception;
        }
    }

    /**
     * Validate the request conditions and retrieve the user.
     *
     * @param Request        $request        The current request
     * @param UserRepository $userRepository The user repository
     *
     * @throws NotFoundHttpException
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

    /**
     * Activate the user and execute the zen command to create the user account in the system.
     *
     * @param User $user The user to activate
     *
     * @throws \Exception
     */
    public function activateUser(User $user): void
    {
        $plainPassword = $this->pendingActivationService->read($user);
        if ($plainPassword !== null) {
            $this->commandExecutorService->execute('zen', ['user', 'create', '-u', $user->getUsername(), '-p', $plainPassword]);
            $user->setActivatedAt(new \DateTimeImmutable());
            $user->setIsVerified(true);
            $this->entityManager->flush();
            $this->pendingActivationService->delete($user);
        }
    }

    /**
     * Send the verification email to the user.
     *
     * @param User $user The user to send the email to
     */
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
