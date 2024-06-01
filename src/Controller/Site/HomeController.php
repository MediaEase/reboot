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

namespace App\Controller\Site;

use App\Repository\UserRepository;
use App\Repository\SettingRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private SettingRepository $settingRepository
    ) {
    }

    #[Route('/', name: 'app_home')]
    #[IsGranted('ROLE_USER')]
    public function index(#[CurrentUser] \App\Entity\User $user): Response
    {
        $profile = $this->userRepository->findMyProfile($user);
        $settings = $this->settingRepository->find(1);

        return $this->render('pages/dashboard/index.html.twig', [
            'user' => $profile,
            'settings' => $settings,
        ]);
    }
}
