<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Group;
use App\Repository\GroupRepository;
use App\Repository\StoreRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ApplicationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/groups', name: 'api_groups_')]
final class GroupController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private GroupRepository $groupRepository,
        private StoreRepository $storeRepository,
        private ApplicationRepository $applicationRepository
    ) {
    }

    #[Route('', name: 'getGroups', methods: ['GET'])]
    public function getGroups(): Response
    {
        $groups = $this->groupRepository->findAll();

        return $this->json($groups, Response::HTTP_OK, [], ['groups' => 'group:info']);
    }

    #[Route('', name: 'createGroup', methods: ['POST'])]
    public function createGroup(Group $group): Response
    {
        $this->entityManager->persist($group);
        $this->entityManager->flush();

        return $this->json(['message' => 'Group created'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'getGroup', methods: ['GET'])]
    public function getGroup(Group $group): Response
    {
        return $this->json($group, Response::HTTP_OK, [], ['groups' => 'group:info']);
    }

    #[Route('/{id}', name: 'deleteGroup', methods: ['DELETE'])]
    public function deleteGroup(Group $group): Response
    {
        $this->entityManager->remove($group);
        $this->entityManager->flush();

        return $this->json(['message' => 'Group deleted'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'updateGroup', methods: ['PUT'])]
    public function updateGroup(Group $group): Response
    {
        $this->entityManager->persist($group);
        $this->entityManager->flush();

        return $this->json(['message' => 'Group updated'], Response::HTTP_OK);
    }

    #[Route('/{id}/apps', name: 'addAppsToGroup', methods: ['POST'])]
    public function addAppsToGroup(Group $group): Response
    {
        return $this->json(['message' => 'Apps added to group'], Response::HTTP_OK);
    }

    #[Route('/{id}/apps', name: 'removeAppsFromGroup', methods: ['DELETE'])]
    public function removeAppsFromGroup(Group $group): Response
    {
        return $this->json(['message' => 'Apps removed from group'], Response::HTTP_OK);
    }
}
