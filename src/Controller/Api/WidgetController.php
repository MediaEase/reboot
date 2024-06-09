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

use App\Entity\User;
use App\Entity\Widget;
use OpenApi\Attributes as OA;
use App\Repository\WidgetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api', name: 'api_widgets_')]
#[IsGranted('ROLE_USER')]
#[OA\Tag(name: 'Widgets')]
final class WidgetController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private WidgetRepository $widgetRepository
    ) {
    }

    #[Route('/widgets', name: 'list', methods: ['GET'])]
    public function list(): Response
    {
        $widgets = $this->widgetRepository->findAll();

        return $this->json(['widgets' => $widgets], Response::HTTP_OK, [], ['groups' => User::GROUP_GET_USER, Widget::GROUP_GET_WIDGET]);
    }
}
