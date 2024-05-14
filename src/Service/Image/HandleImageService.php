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

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * HandleImageService is responsible for managing the upload and processing of various image types.
 *
 * This service interacts with the UploadImageService to handle the uploading of files,
 * and optionally applies image processing such as blurring. It updates the preferences
 * entity with the processed file information.
 */
final class HandleImageService
{
    public function __construct(
        private UploadImageService $uploadImageService,
        private ?LoggerInterface $logger = null
    ) {
        $this->logger = $logger instanceof LoggerInterface ? $logger : new NullLogger();
    }

    /**
     * Handles the file upload for different image types.
     *
     * This method manages the uploading of files and applies optional image processing,
     * such as blurring, based on the specified context.
     *
     * @param \Symfony\Component\Form\FormInterface $form        the form containing the file upload fields
     * @param mixed                                 $preferences the preferences entity to update with the uploaded file information
     * @param string                                $field       the form field name for the file upload
     * @param string                                $context     the context of the upload which determines the directory and image processing rules
     * @param bool                                  $applyBlur   whether to apply a blur effect to the image
     *
     * @throws ProcessFailedException if the image processing fails
     */
    public function handleFileUpload(\Symfony\Component\Form\FormInterface $form, mixed $preferences, string $field, string $context, bool $applyBlur = false): void
    {
        $file = $form->get($field)->getData();
        if ($file) {
            $processedFile = $this->uploadImageService->upload($file, $context);

            if ($applyBlur) {
                $originalPath = $processedFile->getRealPath();
                $blurredFilename = pathinfo($originalPath, PATHINFO_FILENAME).'-blur.'.$processedFile->getExtension();
                $blurredPath = pathinfo($originalPath, PATHINFO_DIRNAME).DIRECTORY_SEPARATOR.$blurredFilename;
                $process = new Process(['convert', $originalPath, '-blur', '0x4', $blurredPath]);

                try {
                    $process->mustRun();
                } catch (ProcessFailedException $exception) {
                    $this->logger->error('Image conversion failed: '.$exception->getMessage());
                    throw $exception;
                }

                $preferences->{'set'.ucfirst($field)}($blurredFilename);
            } else {
                $preferences->{'set'.ucfirst($field)}($processedFile->getFilename());
            }
        }
    }
}
