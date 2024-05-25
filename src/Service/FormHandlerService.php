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

namespace App\Service;

use App\Entity\Mount;
use App\Interface\FormHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Image\HandleImageService;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * FormHandlerService handles form submissions.
 */
final class FormHandlerService implements FormHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private HandleImageService $handleImageService,
        private PathAccessService $pathAccessService,
    ) {
    }

    /**
     * Handles form submission based on the type.
     */
    public function handleFormSubmission(string $type, ?UserInterface $user, object $preferences, FormInterface $form): JsonResponse
    {
        return match ($type) {
            'userImages' => $this->handleUserImagesFormSubmission($form, $preferences),
            'userPreferences' => $this->handleUserPreferencesFormSubmission($preferences),
            'changeUserPass' => $this->handleChangeUserPassFormSubmission($user),
            'addPath' => $this->handleAddPathFormSubmission($user, $form->getData()),
            default => new JsonResponse(['status' => 'error', 'message' => 'Unknown form type']),
        };
    }

    /**
     * Handles user images form submission.
     */
    private function handleUserImagesFormSubmission(FormInterface $form, object $preferences): JsonResponse
    {
        $this->handleImageService->handleFileUpload($form, $preferences, 'backdrop', 'background', true);
        $this->handleImageService->handleFileUpload($form, $preferences, 'avatar', 'avatar');

        $this->entityManager->persist($preferences);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'success', 'message' => 'Your preferences have been updated successfully!']);
    }

    /**
     * Handles user preferences form submission.
     */
    private function handleUserPreferencesFormSubmission(object $preferences): JsonResponse
    {
        $this->entityManager->persist($preferences);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'success', 'message' => 'Your preferences have been updated successfully!']);
    }

    /**
     * Handles change user password form submission.
     */
    private function handleChangeUserPassFormSubmission(object $user): JsonResponse
    {
        $currentPassword = $_POST['currentPassword'];
        $newPassword = $_POST['plainPassword']['first'];
        $confirmPassword = $_POST['plainPassword']['second'];

        if (!password_verify($currentPassword, $user->getPassword())) {
            return new JsonResponse(['status' => 'error', 'message' => 'Your current password is incorrect.']);
        }

        if ($currentPassword === $newPassword) {
            return new JsonResponse(['status' => 'error', 'message' => 'Your new password cannot be the same as your current password.']);
        }

        if ($newPassword !== $confirmPassword) {
            return new JsonResponse(['status' => 'error', 'message' => 'The password fields must match.']);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'success', 'message' => 'Your password has been changed successfully!']);
    }

    /**
     * Handles add path form submission.
     */
    private function handleAddPathFormSubmission(?UserInterface $user, Mount $mount): JsonResponse
    {
        $mount->setPath(rtrim($mount->getPath(), '/'));
        $existingMount = $this->entityManager->getRepository(Mount::class)->findOneBy([
            'user' => $user,
            'path' => $mount->getPath(),
        ]);

        if ($existingMount) {
            return new JsonResponse(['status' => 'error', 'message' => 'You have already added this mount path.']);
        }

        if (!$this->pathAccessService->userCanAccessPath($mount->getPath(), $user->getUserIdentifier())) {
            return new JsonResponse(['status' => 'error', 'message' => 'You do not have permission to access this mount path.']);
        }

        $mount->setUser($user);
        $this->entityManager->persist($mount);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'success', 'message' => 'New mount path has been added successfully!']);
    }
}
