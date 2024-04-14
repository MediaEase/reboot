<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Service\FilterService;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Manages the upload and processing of image files.
 * Uses LiipImagineBundle for image manipulations based on predefined filters and contexts.
 */
final class ImageUploaderService
{
    public function __construct(
        private FilterService $filterService,
        private ParameterBagInterface $parameterBag,
        private Filesystem $filesystem
    ) {
    }

    /**
     * Uploads an image file to a context-specific directory and applies an image filter.
     *
     * @param UploadedFile $uploadedFile the image file uploaded by the user
     * @param string       $context      the context of the upload which determines the directory and image processing rules
     *
     * @return File the processed file, moved to the appropriate directory
     *
     * @throws \InvalidArgumentException                                      if the context provided is invalid
     * @throws \Symfony\Component\HttpFoundation\File\Exception\FileException if the file could not be moved
     */
    public function upload(UploadedFile $uploadedFile, string $context): File
    {
        $uploadDir = $this->getUploadDir($context);
        $filename = uniqid().'.'.$uploadedFile->guessExtension();

        // Move the file to the specified directory
        $uploadedFile = $uploadedFile->move($uploadDir, $filename);

        // Apply image processing using LiipImagineBundle
        $filter = $this->getFilterByContext($context);
        $this->processImage($uploadedFile, $filter);

        return $uploadedFile;
    }

    /**
     * Retrieves the directory path based on the upload context.
     *
     * @param string $context the context which determines the directory
     *
     * @return string the full path to the directory where the file should be stored
     */
    private function getUploadDir(string $context): string
    {
        return $this->parameterBag->get('kernel.project_dir').'/public/uploads/'.$context;
    }

    /**
     * Determines the image filter to apply based on the upload context.
     *
     * @param string $context the context of the upload
     *
     * @return string the filter name associated with the context
     *
     * @throws \InvalidArgumentException if the specified context is not recognized
     */
    private function getFilterByContext(string $context): string
    {
        return match ($context) {
            'avatar' => 'avatar',
            'background' => 'background',
            'favicon', 'brand_logo' => 'brand_logo',
            'splashscreen' => 'splashscreen',
            'app' => 'app',
            default => throw new \InvalidArgumentException('Invalid context')
        };
    }

    /**
     * Applies the specified image filter to the file.
     *
     * @param File   $file   the file to process
     * @param string $filter the name of the filter to apply
     *
     * @throws \RuntimeException if the file does not exist in the filesystem
     */
    private function processImage(File $file, string $filter): void
    {
        $path = $file->getRealPath();
        if (!$this->filesystem->exists($path)) {
            throw new \RuntimeException('File does not exist');
        }

        // Generate the URL of the filtered image which also caches the processed image
        $this->filterService->getUrlOfFilteredImage($path, $filter);
    }
}
