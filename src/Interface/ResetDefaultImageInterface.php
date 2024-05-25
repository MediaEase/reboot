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

use App\Entity\Preference;

interface ResetDefaultImageInterface
{
    /**
     * Resets the image for a given preference to the default image.
     *
     * @param Preference $preference the preference entity containing the image field
     * @param string     $type       the type of image to reset (e.g., 'avatar', 'background')
     */
    public function resetToDefaultImage(Preference $preference, string $type): void;
}
