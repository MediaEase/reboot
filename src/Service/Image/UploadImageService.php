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

use Liip\ImagineBundle\Exception\Imagine\Filter\NonExistingFilterException;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Service for handling image uploads and processing using LiipImagineBundle.
 *
 * This service manages the uploading of image files, applies various image processing
 * filters, and provides URLs for the processed images.
 */
final class UploadImageService
{
    public function __construct(
        private DataManager $dataManager,
        private FilterManager $filterManager,
        private CacheManager $cacheManager,
        private ParameterBagInterface $parameterBag,
        private Filesystem $filesystem,
        private ?LoggerInterface $logger = null
    ) {
        $this->logger = $logger instanceof LoggerInterface ? $logger : new NullLogger();
    }

    /**
     * Uploads a file and processes it using LiipImagineBundle.
     * Returns the uploaded file.
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
     * Returns the absolute path to the upload directory for the specified context.
     *
     * @param string $context the context which determines the directory
     *
     * @return string the full path to the directory where the file should be stored
     *
     * @throws \InvalidArgumentException if the context is invalid
     */
    private function getUploadDir(string $context): string
    {
        return $this->parameterBag->get('kernel.project_dir').'/public/uploads/'.$context.'/custom';
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
     * Processes the image using LiipImagineBundle.
     *
     * @param File   $file   the file to process
     * @param string $filter the name of the filter to apply
     *
     * @throws \RuntimeException          if the file does not exist
     * @throws NonExistingFilterException if the filter does not exist
     */
    private function processImage(File $file, string $filter): void
    {
        $publicDir = $this->parameterBag->get('kernel.project_dir').'/public';
        $relativePath = str_replace($publicDir.'/', '', $file->getPathname());

        if (!$this->filesystem->exists($file->getPathname())) {
            throw new \RuntimeException('File does not exist');
        }

        $this->warmUpCache($relativePath, $filter);
    }

    /**
     * Warms up the cache for the specified path and filter.
     *
     * @param string $path   the path to the image
     * @param string $filter the filter to apply
     *
     * @throws NonExistingFilterException if the filter does not exist
     * @throws \Exception                 if an error occurs while storing the filtered image
     */
    private function warmUpCache(string $path, string $filter): void
    {
        try {
            $binary = $this->dataManager->find($filter, $path);
            $filteredBinary = $this->filterManager->applyFilter($binary, $filter);
            $this->cacheManager->store($filteredBinary, $path, $filter);
        } catch (NonExistingFilterException $nonExistingFilterException) {
            $this->logger->debug(sprintf(
                'Could not locate filter "%s" for path "%s". Message was "%s"',
                $filter,
                $path,
                $nonExistingFilterException->getMessage()
            ));

            throw $nonExistingFilterException;
        }
    }

    /**
     * Returns the URL of the filtered image.
     *
     * @param string $path   the path to the image
     * @param string $filter the filter to apply
     *
     * @return string the URL of the filtered image
     *
     * @throws \Exception if an error occurs while generating the URL
     */
    public function getUrlOfFilteredImage(string $path, string $filter): string
    {
        try {
            return $this->cacheManager->resolve($path, $filter);
        } catch (\Exception $exception) {
            $this->logger->error(sprintf(
                'Error generating URL for path "%s" with filter "%s": %s',
                $path,
                $filter,
                $exception->getMessage()
            ));
            throw $exception;
        }
    }
}
