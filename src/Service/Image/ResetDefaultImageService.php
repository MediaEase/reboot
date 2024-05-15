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

namespace App\Service\Image;

use App\Entity\Setting;
use App\Entity\Preference;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Service to provide paths to default images for specific contexts.
 *
 * This service helps retrieve the paths to default images for various contexts
 * such as avatars, backgrounds, brand logos, favicons, app store images, and splash screens.
 * It also handles resetting images to their default state if they are not default images.
 */
final class ResetDefaultImageService
{
    public function __construct(
        private ParameterBagInterface $parameterBag,
        private EntityManagerInterface $entityManager,
        private Filesystem $filesystem
    ) {
    }

    /**
     * Retrieves the path to the current image for a given entity and context.
     *
     * @param object $entity  the entity containing the image field
     * @param string $context The context of the image (e.g., 'avatar', 'background').
     *
     * @return string|null the path to the current image or null if not found
     *
     * @throws \InvalidArgumentException if the context is invalid or not supported
     */
    public function getCurrentImagePath(object $entity, string $context): ?string
    {
        $imageField = $this->getImageFieldByContext($entity, $context);
        $currentImage = $entity->{'get'.ucfirst($imageField)}();

        return $this->buildPath($context, $currentImage);
    }

    /**
     * Retrieves the path to the default image for a given context.
     *
     * @param string $context The context of the image (e.g., 'avatar', 'background', 'brand', 'appstore', 'splashscreen').
     *
     * @return string the path to the default image
     *
     * @throws \InvalidArgumentException if the context is invalid or not supported
     */
    public function getDefaultImagePath(string $context): string
    {
        return match ($context) {
            'avatar' => $this->buildPath($context, 'default.png'),
            'background' => $this->buildPath($context, 'default.png'),
            'brand' => $this->buildPath('brand', 'default-logo.png'),
            'favicon' => $this->buildPath('brand', 'default-favicon.png'),
            'appstore' => $this->buildPath('brand', 'default-appstore.png'),
            'splashscreen' => $this->buildPath($context, 'default.png'),
            default => throw new \InvalidArgumentException('Invalid or unsupported context: '.$context),
        };
    }

    /**
     * Resets the image to the default if it is not already the default image.
     *
     * @param object $entity  the entity containing the image field
     * @param string $context The context of the image (e.g., 'avatar', 'background').
     *
     * @throws \Exception if an error occurs while removing the existing image
     */
    public function resetToDefaultImage(object $entity, string $context): void
    {
        $imageField = $this->getImageFieldByContext($entity, $context);
        $currentImage = $entity->{'get'.ucfirst($imageField)}();
        $defaultImage = $this->getDefaultImagePath($context);

        if (!str_starts_with($currentImage, 'default')) {
            $currentImagePath = $this->getCurrentImagePath($entity, $context);
            if (str_contains($currentImagePath, '-blur')) {
                $currentImagePath = str_replace('-blur', '', $currentImagePath);
            }

            $pattern = $this->getGlobPattern($currentImagePath);

            foreach (glob($pattern) as $filePath) {
                if ($this->filesystem->exists($filePath)) {
                    $this->filesystem->remove($filePath);
                }
            }

            $entity->{'set'.ucfirst($imageField)}(basename($defaultImage));
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        }
    }

    /**
     * Constructs the full file path for a given context and file within the public uploads directory.
     *
     * @param string $context  the context within the uploads folder
     * @param string $filename the filename within the specified directory
     *
     * @return string the full path to the file
     */
    private function buildPath(string $context, string $filename): string
    {
        if (!str_starts_with($filename, 'default')) {
            return $this->parameterBag->get('kernel.project_dir').'/public/uploads/'.$context.'/custom/'.$filename;
        }

        return $this->parameterBag->get('kernel.project_dir').'/public/uploads/'.$context.'/'.$filename;
    }

    /**
     * Determines the image field name by the context and entity.
     *
     * @param object $entity  the entity containing the image field
     * @param string $context The context of the image (e.g., 'avatar', 'background').
     *
     * @return string the name of the image field
     *
     * @throws \InvalidArgumentException if the context is invalid or not supported
     */
    private function getImageFieldByContext(object $entity, string $context): string
    {
        if ($entity instanceof Preference) {
            return match ($context) {
                'avatar' => 'avatar',
                'background' => 'backdrop',
                default => throw new \InvalidArgumentException('Invalid or unsupported context for Preference entity: '.$context),
            };
        }

        if ($entity instanceof Setting) {
            return match ($context) {
                'brand' => 'brand',
                'favicon' => 'favicon',
                'splashscreen' => 'splashscreen',
                'appstore' => 'appstore',
                default => throw new \InvalidArgumentException('Invalid or unsupported context for Setting entity: '.$context),
            };
        }

        throw new \InvalidArgumentException('Unsupported entity type: '.$entity::class);
    }

    /**
     * Get the glob pattern for finding all variations of an image.
     *
     * @param string $originalPath the path of the original image
     *
     * @return string the glob pattern to find all variations of the image
     */
    private function getGlobPattern(string $originalPath): string
    {
        $directory = pathinfo($originalPath, PATHINFO_DIRNAME);
        $filename = pathinfo($originalPath, PATHINFO_FILENAME);
        $extension = pathinfo($originalPath, PATHINFO_EXTENSION);

        return $directory.DIRECTORY_SEPARATOR.$filename.'*.'.$extension;
    }
}
