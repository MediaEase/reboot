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

namespace App\Interface;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface UploadImageInterface
{
    /**
     * Uploads a file and processes it using LiipImagineBundle.
     * Returns the uploaded file.
     *
     * @param UploadedFile $uploadedFile the image file uploaded by the user
     * @param string       $context      the context of the upload which determines the directory and image processing rules
     *
     * @return File the processed file, moved to the appropriate directory
     */
    public function upload(UploadedFile $uploadedFile, string $context): File;

    /**
     * Returns the URL of the filtered image.
     *
     * @param string $path   the path to the image
     * @param string $filter the filter to apply
     *
     * @return string the URL of the filtered image
     */
    public function getUrlOfFilteredImage(string $path, string $filter): string;
}
