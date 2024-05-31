<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Setting;
use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/settings', name: 'api_settings_')]
#[IsGranted('ROLE_USER')]
final class SettingController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SettingRepository $settingRepository
    ) {
    }

    #[Route('', name: 'show', methods: ['GET'])]
    public function show(): Response
    {
        $settings = $this->settingRepository->findLast();

        return $this->json($settings, Response::HTTP_OK, [], ['groups' => [Setting::GROUP_GET_SETTINGS]]);
    }
}
