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

namespace App\Controller\Security;

use App\Entity\User;
use App\Entity\Setting;
use App\Form\CreateUserType;
use App\Form\RegistrationType;
use App\Security\EmailVerifier;
use App\Repository\UserRepository;
use App\Handler\RegistrationHandler;
use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

final class RegistrationController extends AbstractController
{
    public function __construct(
        private EmailVerifier $emailVerifier,
        private RegistrationHandler $registrationHandler,
        private SettingRepository $settingRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/register', name: 'app_register', defaults: ['_feature' => 'registration'])]
    public function register(Request $request): Response
    {
        if ($this->getUser() instanceof \Symfony\Component\Security\Core\User\UserInterface) {
            return $this->redirectToRoute('app_home');
        }

        if ($this->settingRepository->findLast()->isRegistrationEnabled() === false) {
            return $this->redirectToRoute('app_login');
        }

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->registrationHandler->handleRegistration(
                $form->getData(),
                $form->getData()->getPlainPassword()
            );
            $this->addFlash('success', 'Your account has been created. Please check your email for a verification link.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'errors' => $form->getErrors(true, false),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(
        Request $request,
        TranslatorInterface $translator,
        UserRepository $userRepository
    ): Response {
        $user = $this->registrationHandler->validateConditions($request, $userRepository);
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $verifyEmailException) {
            $message = $translator->trans($verifyEmailException->getReason(), [], 'VerifyEmailBundle');
            $this->addFlash('verify_email_error', $message);

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }

    #[Route('/settings/users/create', name: 'app_settings_users_create', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function createUser(#[CurrentUser] User $user, Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(CreateUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $formField = $error->getOrigin();
                $errors[$formField->getName()] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->registrationHandler->handleRegistration($user, $form->get('plainPassword')->get('first')->getData(), 'admin_creation');
            $this->addFlash('success', 'User created successfully.');

            return new JsonResponse(['redirectUrl' => $this->generateUrl('app_settings_users_list')]);
        }

        return $this->render('pages/users/create/create_user.html.twig', [
            'createUserForm' => $form->createView(),
            'users' => $this->entityManager->getRepository(User::class)->findAll(),
            'settings' => $this->entityManager->getRepository(Setting::class)->findLast(),
            'user' => $user,
        ]);
    }
}
