<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\PreferenceRepository;
use App\Repository\ServiceRepository;
use App\Repository\WidgetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class HomeController extends AbstractController
{
    public function __construct(
        private WidgetRepository $widgetRepository,
        private PreferenceRepository $preferenceRepository,
        private ServiceRepository $serviceRepository
    ) {
    }

    #[Route('/', name: 'app_home')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        $user = $this->getUser();
        $preferences = $this->preferenceRepository->findOneBy(['user' => $user]);
        $services = $this->serviceRepository->findBy(['user' => $user]);
        // sort services by name alphabetically
        usort($services, static function ($a, $b): int {
            return strcmp($a->getName(), $b->getName());
        });
        $pinnedAppIds = $preferences->getPinnedApps();
        $pinnedServices = [];

        foreach ($pinnedAppIds as $pinnedAppId) {
            $service = $this->serviceRepository->findOneBy(['id' => $pinnedAppId]);
                if ($service !== null) {
                    $pinnedServices[] = $service;
                }
        }

        usort($pinnedServices, static function ($a, $b): int {
            return strcmp($a->getName(), $b->getName());
        });

        return $this->render('dashboard/index.html.twig', [
            'user' => $user,
            'preferences' => $preferences,
            'widgetsRepository' => $this->widgetRepository,
            'apps' => $services,
            'pinnedApps' => $pinnedServices,
        ]);
    }
}
