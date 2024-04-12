<?php

declare(strict_types=1);

namespace App\Controller;

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

        return $this->render('dashboard/index.html.twig', [
            'user' => $profile,
            'settings' => $settings,
        ]);
    }
}
