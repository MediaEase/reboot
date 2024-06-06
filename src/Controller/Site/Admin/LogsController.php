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

use App\Form\Setting\LogLevelType;
use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route('/system/logs', name: 'app_settings_logs_')]
#[IsGranted('ROLE_ADMIN')]
class LogsController extends AbstractController
{
    public function __construct(
        private SettingRepository $settingRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/access-logs', name: 'access', methods: ['GET'])]
    public function logs(#[CurrentUser] $user): Response
    {
        return $this->render('pages/settings/logs/access_logs.html.twig', [
            'user' => $user,
            'settings' => $this->settingRepository->findLast(),
        ]);
    }

    #[Route('/application-logs', name: 'application', methods: ['GET', 'POST'])]
    public function applicationLogs(#[CurrentUser] $user, Request $request): Response
    {
        $settings = $this->settingRepository->findLast();

        $form = $this->createForm(LogLevelType::class, $settings);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($settings);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_settings_logs_application', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pages/settings/logs/application_logs.html.twig', [
            'user' => $user,
            'settings' => $settings,
            'form' => $form->createView(),
        ]);
    }
}
