<?php

declare(strict_types=1);

namespace App\Service\Image;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Service to provide paths to default images for specific contexts.
 */
final class ResetDefaultImageService
{
    public function __construct(
        private ParameterBagInterface $parameterBag
    ) {
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
     * Constructs the full file path for a given directory and file within the public uploads directory.
     *
     * @param string $directory the directory within the uploads folder
     * @param string $filename  the filename within the specified directory
     *
     * @return string the full path to the file
     */
    private function buildPath(string $directory, string $filename): string
    {
        return $this->parameterBag->get('kernel.project_dir').'/public/uploads/'.$directory.'/'.$filename;
    }
}
