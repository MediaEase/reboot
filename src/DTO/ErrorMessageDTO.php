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

namespace App\DTO;

/**
 * Data Transfer Object for error messages.
 */
final class ErrorMessageDTO
{
    /**
     * Constructor.
     *
     * @param string $locale The locale of the message.
     * @param string $localeOrigin The origin of the locale.
     * @param string $text The text of the message.
     */
    public function __construct(
        public string $locale, 
        public string $localeOrigin, 
        public string $text
    )
    {
    }
}
