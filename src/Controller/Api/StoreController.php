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
        $data = json_decode($request->getContent(), true);
        $appId = $data['appId'] ?? null;
        $action = $data['action'] ?? null;
        if ($action === 'install') {
            $action = 'add';
        }
        $user = $this->getUser();

        if (!$appId || !$action) {
            return $this->json(['error' => 'Invalid request data'], Response::HTTP_BAD_REQUEST);
        }


        $scriptPath = '/usr/local/bin/zen';
        $process = new Process(['sudo', $scriptPath, 'software', $action, $appId]);
        $process->setTimeout(1500);
        $process->setIdleTimeout(1500);

        try {
            $process->mustRun();

            $output = explode(PHP_EOL, $process->getOutput());

            return $this->json(['output' => $output], Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->storeLogger->error('Script execution failed', ['exception' => $e]);

            return $this->json(['error' => 'Script execution failed'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

