<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Metric\CpuMetricInterface;
use App\Metric\DiskMetricInterface;
use App\Metric\NetworkMetricInterface;
use App\Metric\RamMetricInterface;
use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/metric', name: 'api_metric_')]
#[IsGranted('ROLE_USER')]
final class MetricController extends AbstractController
{
    public function __construct(
        private CpuMetricInterface $cpuMetric,
        private DiskMetricInterface $diskMetric,
        private NetworkMetricInterface $networkMetric,
        private RamMetricInterface $ramMetric,
        private ServiceRepository $serviceRepository
    ) {
    }

    #[Route('/cpu', name: 'cpu', methods: ['GET'])]
    public function getCpuUsage(): JsonResponse
    {
        $cpuUsage = $this->cpuMetric->getCpuInfo();

        return $this->json(['cpu' => $cpuUsage]);
    }

    #[Route('/mem', name: 'mem', methods: ['GET'])]
    public function getRamUsage(): JsonResponse
    {
        $ramUsage = $this->ramMetric->getMemoryUsage();

        return $this->json(['ram' => $ramUsage]);
    }

    #[Route('/disk', name: 'disk', methods: ['GET'])]
    public function getDiskUsage(): JsonResponse
    {
        $diskUsage = $this->diskMetric->getDiskUsage();

        return $this->json(['disk' => $diskUsage]);
    }

    #[Route('/net', name: 'network', methods: ['GET'])]
    public function getNetworkUsage(): JsonResponse
    {
        $networkUsage = $this->networkMetric->getNetworkUsage();

        return $this->json(['network' => $networkUsage]);
    }

    #[Route('/clients', name: 'clients', methods: ['GET'])]
    public function getClientState(#[CurrentUser] ?User $user): JsonResponse
    {
        $userApps = $this->serviceRepository->findBy(['user' => $user], ['application' => 'ASC']);
        $userAppsNames = new ArrayCollection();
        foreach ($userApps as $userApp) {
            $userAppsNames->add($userApp->getApplication()->getName());
        }

        return $this->json(['error' => 'No torrent client installed'], JsonResponse::HTTP_NOT_FOUND);
    }
}
