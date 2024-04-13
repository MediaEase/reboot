<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\Store;
use App\Entity\Application;
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
use OpenApi\Attributes as OA;

#[Route('/api/store', name: 'api_store_')]
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

    #[Route('', name: 'list', methods: ['GET'])]
    #[OA\Get(
        operationId: 'getStoreList',
        summary: 'Get a list of stores',
        description: 'Retrieve all available stores',
        tags: ['Store'],
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'OK',
        content: new OA\JsonContent(ref: '#/components/schemas/Store.list'),
    )]
    public function list(): Response
    {
        $stores = $this->storeRepository->findAll();

        return $this->json($stores, Response::HTTP_OK, [], ['groups' => [Store::GROUP_GET_STORES, Application::GROUP_GET_APPLICATIONS, User::GROUP_GET_USER]]);
    }

    #[Route('/install', name: 'install', methods: ['POST'])]
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
        } catch (\Exception) {
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
