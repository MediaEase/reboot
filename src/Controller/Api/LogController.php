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

namespace App\Controller\Api;

use App\Repository\UserRepository;
use App\Command\Logs\TailLogCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/logs', name: 'api_logs_')]
#[IsGranted('ROLE_ADMIN')]
final class LogController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private TailLogCommand $tailLogCommand
    ) {
    }

    #[Route('/fetch', name: 'fetch', methods: ['POST'])]
    public function fetchLogContent(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['filePath'])) {
            return new JsonResponse(['error' => 'File path not provided'], 400);
        }

        $filePath = $data['filePath'];

        if (!file_exists($filePath)) {
            return new JsonResponse(['error' => 'File not found'], 404);
        }

        $arrayInput = new ArrayInput([
            'logfile' => $filePath,
        ]);
        $bufferedOutput = new BufferedOutput();
        try {
            $this->tailLogCommand->run($arrayInput, $bufferedOutput);
            $logContent = $bufferedOutput->fetch();
        } catch (\Exception $exception) {
            return new JsonResponse(['error' => 'Failed to fetch log content', 'details' => $exception->getMessage()], 500);
        }

        return new JsonResponse(['logs' => $logContent], 200);
    }
}
