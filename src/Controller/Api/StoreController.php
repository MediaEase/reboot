<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Psr\Log\LoggerInterface;
use Symfony\UX\Turbo\TurboBundle;
use App\Repository\StoreRepository;
use Symfony\Component\Process\Process;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ApplicationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api', name: 'api_apps_')]
#[IsGranted('ROLE_USER')]
final class StoreController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private StoreRepository $storeRepository,
        private ApplicationRepository $applicationRepository,
        private LoggerInterface $storeLogger
    ) {
    }

    #[Route('/store', name: 'getStore', methods: ['GET'])]
    public function getStore(): Response
    {
        $stores = $this->storeRepository->findAll();

        return $this->json($stores, Response::HTTP_OK, [], ['groups' => 'store:info']);
    }

    #[Route('/store/install', name: 'store_install', methods: ['POST'])]
    public function install(Request $request): Response
    {
        $scriptPath = '/usr/local/bin/zen';
        $process = new Process(['sudo', $scriptPath]);
        $process->setTimeout(1500);
        $process->setIdleTimeout(1500);

        try {
            $process->mustRun();

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

                return $this->renderBlock('store/install.html.twig', 'success_stream', [
                    'message' => 'Script exécuté avec succès',
                ]);
            }

            return $this->redirectToRoute('store_success');
        } catch (\Exception $exception) {
            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

                return $this->renderBlock('store/install.html.twig', 'error_stream', [
                    'message' => 'Échec de l\'exécution du script',
                ]);
            }

            return $this->redirectToRoute('store_error');
        }
    }
}
