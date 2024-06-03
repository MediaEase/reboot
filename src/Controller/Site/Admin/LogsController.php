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

namespace App\Controller\Site\Admin;

use App\Entity\Log;
use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/system/logs', name: 'app_settings_logs_')]
#[IsGranted('ROLE_ADMIN')]
class LogsController extends AbstractController
{
    public function __construct(private SettingRepository $settingRepository, private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/access-logs', name: 'access', methods: ['GET'])]
    public function logs(#[CurrentUser] $user): Response
    {
        return $this->render('pages/settings/logs/access_logs.html.twig', [
            'logs' => $this->entityManager->getRepository(Log::class)->findBy([], ['timestamp' => 'DESC']),
            'user' => $user,
            'settings' => $this->settingRepository->findLast(),
        ]);
    }

    #[Route('/application-logs', name: 'application', methods: ['GET'])]
    public function applicationLogs(#[CurrentUser] $user): Response
    {
        return $this->render('pages/settings/logs/application_logs.html.twig', [
            'user' => $user,
            'settings' => $this->settingRepository->findLast(),
        ]);
    }
}