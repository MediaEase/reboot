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

namespace App\Controller\Site;

use App\Entity\Mount;
use App\Form\User\MountType;
use App\Repository\MountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/mount')]
class MountController extends AbstractController
{
    #[Route('/', name: 'app_mount_index', methods: ['GET'])]
    public function index(MountRepository $mountRepository): Response
    {
        return $this->render('mount/index.html.twig', [
            'mounts' => $mountRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_mount_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $mount = new Mount();
        $form = $this->createForm(MountType::class, $mount);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($mount);
            $entityManager->flush();

            return $this->redirectToRoute('app_mount_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('mount/new.html.twig', [
            'mount' => $mount,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_mount_show', methods: ['GET'])]
    public function show(Mount $mount): Response
    {
        return $this->render('mount/show.html.twig', [
            'mount' => $mount,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_mount_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Mount $mount, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MountType::class, $mount);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_mount_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('mount/edit.html.twig', [
            'mount' => $mount,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_mount_delete', methods: ['POST'])]
    public function delete(Request $request, Mount $mount, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mount->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($mount);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_mount_index', [], Response::HTTP_SEE_OTHER);
    }
}
