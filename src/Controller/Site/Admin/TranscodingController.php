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

use App\Entity\Service;
use App\Service\TranscodingService;
use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/settings', name: 'app_settings_extras_', defaults: ['_feature' => 'transcoding'], methods: ['GET'])]
#[IsGranted('ROLE_ADMIN')]
class TranscodingController extends AbstractController
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $entityManager,
        private TranscodingService $transcodingService,
        private SettingRepository $settingRepository
    ) {
    }

    #[Route('/transcoding', name: 'transcoding', methods: ['GET'])]
    public function transcode(): Response
    {
        if ($this->getParameter('app.mediaease_init') === false) {
            return $this->redirectToRoute('app_settings_general');
        }

        $provider = $this->getParameter('app.provider');
        if ($provider === 'hetzner') {
            return $this->redirectToRoute('app_settings_general');
        }

        $services = $this->entityManager->getRepository(Service::class)->findAll();
        $services = array_filter($services, static function ($service): bool {
            return in_array($service->getApplication()->getName(), ['Plex', 'Emby', 'Jellyfin'], true);
        });
        usort($services, static function ($a, $b): int {
            return strcmp($a->getName(), $b->getName());
        });

        return $this->render('pages/settings/transcoding.html.twig', [
            'apps' => $services,
            'settings' => $this->settingRepository->findLast(),
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/transcoding/enable/{id}', name: 'transcoding_enable', methods: ['POST'])]
    public function enable(Service $service): Response
    {
        $this->transcodingService->enableTranscoding($service);
        $this->addFlash('success', 'The transcoding has been enabled for the service'.$service->getName().'.');

        return $this->redirectToRoute('admin_transcoding', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/transcoding/disable/{id}', name: 'transcoding_disable', methods: ['POST'])]
    public function disable(Service $service): Response
    {
        $this->transcodingService->disableTranscoding($service);
        $this->addFlash('success', 'The transcoding has been disabled for the service'.$service->getName().'.');

        return $this->redirectToRoute('admin_transcoding', [
            'user' => $this->getUser(),
        ]);
    }
}
