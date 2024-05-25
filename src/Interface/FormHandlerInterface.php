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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;

interface FormHandlerInterface
{
    /**
     * Handle the form submission.
     *
     * @return Response
     */
    public function handleFormSubmission(string $type, ?UserInterface $user, object $preferences, FormInterface $form): JsonResponse;
}
