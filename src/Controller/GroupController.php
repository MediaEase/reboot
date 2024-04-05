<?php

namespace App\Controller;

use App\Entity\Group;
use App\Form\Setting\AppGroupType;
use App\Form\Setting\NewGroupType;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/access-groups', name: 'app_settings_access_groups_')]
class GroupController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(GroupRepository $groupRepository): Response
    {
        $user = $this->getUser();
        $groups = $groupRepository->findAll();
        $editableGroups = ['media', 'automation', 'download', 'full', 'remote'];
        $groupApplications = [];
        foreach ($groups as $group) {
            $isEditable = in_array($group->getName(), $editableGroups, true);

            $groupApplications[$group->getId()] = [
                'group' => $group,
                'applications' => [],
                'editable' => $isEditable,
            ];

            $stores = $group->getStores();
            foreach ($stores as $store) {
                $applications = $store->getApplication();
                $groupApplications[$group->getId()]['applications'][] = $applications;
            }

            usort($groupApplications, static function (array $a, array $b) : int {
                $groupNameA = strtolower($a['group']->getName());
                $groupNameB = strtolower($b['group']->getName());
                if ($groupNameA === 'full') {
                    return -1;
                }

                if ($groupNameB === 'full') {
                    return 1;
                }

                return strcmp($groupNameA, $groupNameB);
            });
        }

        return $this->render('settings/access_groups/index.html.twig', [
            'groupApplications' => $groupApplications,
            'user' => $user,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $group = new Group();
        $form = $this->createForm(NewGroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($group);
            $entityManager->flush();

            return $this->redirectToRoute('app_group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('settings/access_groups/new.html.twig', [
            'group' => $group,
            'form' => $form,
            'user' => $user,
        ]);
    }

    #[Route('/{id}', name: 'show_group', methods: ['GET'])]
    public function show(Group $group): Response
    {
        $user = $this->getUser();

        return $this->render('settings/access_groups/show.html.twig', [
            'group' => $group,
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Group $group, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $groups = ['media', 'automation', 'download', 'full', 'remote'];

        if (in_array($group->getName(), $groups, true)) {
            return $this->redirectToRoute('app_group_index');
        }

        $form = $this->createForm(AppGroupType::class, $group);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (in_array($group->getName(), $groups, true)) {
                return $this->redirectToRoute('app_group_show', ['id' => $group->getId()]);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('settings/access_groups/edit.html.twig', [
            'group' => $group,
            'form' => $form,
            'user' => $user,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Group $group, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$group->getId(), $request->request->get('_token'))) {
            $entityManager->remove($group);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_group_index', [], Response::HTTP_SEE_OTHER);
    }
}