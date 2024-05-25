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

namespace App\Controller\Site;

use App\Entity\Setting;
use App\Entity\Preference;
use App\Service\Image\ResetDefaultImageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * This controller provides endpoints to reset images for Setting and Preference entities,
 * replacing custom images with their default versions.
 */
#[Route('/reset-image', name: 'reset_image_', methods: ['POST'])]
final class ImageResetController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ResetDefaultImageService $resetDefaultImageService
    ) {
    }

    /**
     * Resets the image to the default for a given Setting context.
     *
     * This method handles resetting the image for a Setting entity to its default state.
     * It identifies the Setting entity, checks the current image, and replaces it with the default image if necessary.
     *
     * @param string $context The context of the image (e.g., 'brand', 'favicon', 'splashscreen', 'appstore').
     *
     * @return Response the response indicating the result of the operation
     *
     * @throws \InvalidArgumentException if the context is invalid or not supported
     */
    #[Route('/setting/{context}', name: 'setting', methods: ['POST'])]
    public function resetSettingImage(string $context): Response
    {
        $setting = $this->entityManager->getRepository(Setting::class)->find(1);

        if (!$setting) {
            return new Response('Setting not found.', Response::HTTP_NOT_FOUND);
        }

        return $this->resetImage($setting, $context);
    }

    /**
     * Resets the image to the default for a given Preference context.
     *
     * This method handles resetting the image for a Preference entity to its default state.
     * It identifies the Preference entity associated with the current user, checks the current image,
     * and replaces it with the default image if necessary.
     *
     * @param string $context The context of the image (e.g., 'avatar', 'background').
     *
     * @return Response the response indicating the result of the operation
     *
     * @throws \InvalidArgumentException if the context is invalid or not supported
     */
    #[Route('/preference/{context}', name: 'preference', methods: ['POST'])]
    public function resetPreferenceImage(string $context): Response
    {
        $user = $this->getUser();
        $preference = $this->entityManager->getRepository(Preference::class)->findOneBy(['user' => $user]);

        if (!$preference) {
            return new Response('Preference not found.', Response::HTTP_NOT_FOUND);
        }

        return $this->resetImage($preference, $context);
    }

    /**
     * Resets the image to the default for a given entity and context.
     *
     * This private method handles the common logic for resetting images to their default state.
     * It checks the current image and replaces it with the default image if necessary.
     *
     * @param object $entity  the entity containing the image field
     * @param string $context The context of the image (e.g., 'avatar', 'background').
     *
     * @return Response the response indicating the result of the operation
     *
     * @throws \InvalidArgumentException if the context is invalid or not supported
     */
    private function resetImage(object $entity, string $context): Response
    {
        try {
            $this->resetDefaultImageService->resetToDefaultImage($entity, $context);
            $this->addFlash('success', 'Image reset to default successfully.');

            return $this->redirectToRoute('app_users_profile');
        } catch (\InvalidArgumentException $invalidArgumentException) {
            $this->addFlash('error', $invalidArgumentException->getMessage());

            return new Response($invalidArgumentException->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
