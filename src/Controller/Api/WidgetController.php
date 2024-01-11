<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\WidgetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api', name: 'api_widgets_')]
#[IsGranted('ROLE_USER')]
final class WidgetController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private WidgetRepository $widgetRepository
    ) {
    }

    #[Route('/widgets', name: 'getWidgets', methods: ['GET'])]
    public function getWidgets(): Response
    {
        $widgets = $this->widgetRepository->findAll();

        return $this->json($widgets, Response::HTTP_OK, [], ['groups' => 'widget:info']);
    }
}
