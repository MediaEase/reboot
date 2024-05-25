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

use Symfony\Component\Form\FormInterface;
use App\Entity\Preference;

interface HandleImageInterface
{
    /**
     * Handles file upload.
     *
     * @param FormInterface $form         the form
     * @param Preference    $preference   the preferences
     * @param string        $field        the field
     * @param string        $type         the type
     * @param bool          $isBackground whether the image is a background image
     */
    public function handleFileUpload(FormInterface $form, Preference $preference, string $field, string $type, bool $isBackground = false): void;
}
