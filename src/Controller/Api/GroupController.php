<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Group;
use App\Entity\Store;
use App\Entity\Application;
use App\Repository\GroupRepository;
use App\Repository\StoreRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ApplicationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/groups', name: 'api_groups_')]
#[IsGranted('ROLE_USER')]
final class GroupController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private GroupRepository $groupRepository,
        private StoreRepository $storeRepository,
        private ApplicationRepository $applicationRepository
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): Response
    {
        $groups = $this->groupRepository->findAll();

        return $this->json($groups, Response::HTTP_OK, [], ['groups' => Store::GROUP_GET_STORES]);
    }

    #[Route('', name: 'add', methods: ['POST'])]
    public function add(Group $group): Response
    {
        $this->entityManager->persist($group);
        $this->entityManager->flush();

        return $this->json(['message' => 'Group created'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Group $group): Response
    {
        return $this->json($group, Response::HTTP_OK, [], ['groups' => [Store::GROUP_GET_STORES]]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Group $group): Response
    {
        $this->entityManager->remove($group);
        $this->entityManager->flush();

        return $this->json(['message' => 'Group deleted'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Group $group): Response
    {
        $this->entityManager->persist($group);
        $this->entityManager->flush();

        return $this->json(['message' => 'Group updated'], Response::HTTP_OK);
    }

    #[Route('/{id}/apps', name: 'addAppsToGroup', methods: ['POST'])]
    public function addAppsToGroup(Group $group, Application $application): Response
    {
        return $this->json(['message' => 'Apps added to group'], Response::HTTP_OK);
    }

    #[Route('/{id}/apps', name: 'removeAppsFromGroup', methods: ['DELETE'])]
    public function removeAppsFromGroup(Group $group): Response
    {
        return $this->json(['message' => 'Apps removed from group'], Response::HTTP_OK);
    }
}
