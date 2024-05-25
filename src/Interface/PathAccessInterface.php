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

interface PathAccessInterface
{
    /**
     * Checks if a user can access the specified path.
     *
     * @param string $path     the path to check
     * @param string $username the username to check
     *
     * @return bool true if the user can access the path, false otherwise
     */
    public function userCanAccessPath(string $path, string $username): bool;
}
