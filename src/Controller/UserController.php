<?php

namespace App\Controller;

use App\Form\User\UserImageType;
use Psr\Log\LoggerInterface;
use App\Repository\SettingRepository;
use App\Repository\PreferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Image\HandleImageService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(name: 'app_users_')]
class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SettingRepository $settingRepository,
        private PreferenceRepository $preferenceRepository,
        private LoggerInterface $logger,
        private HandleImageService $handleImageService
    ) {
    }

    #[Route('/me', name: 'profile', methods: ['GET', 'POST'])]
    public function profile(Request $request): Response
    {
        $user = $this->getUser();
        $preferences = $this->preferenceRepository->findOneBy(['user' => $user]);
        $userImagesForm = $this->createForm(UserImageType::class, $preferences);
        $userImagesForm->handleRequest($request);
        if ($userImagesForm->isSubmitted() && $userImagesForm->isValid()) {
            $this->handleImageService->handleFileUpload($userImagesForm, $preferences, 'backdrop', 'background', true);
            $this->handleImageService->handleFileUpload($userImagesForm, $preferences, 'avatar', 'avatar');
            $this->entityManager->persist($preferences);
            $this->entityManager->flush();
            $this->addFlash('success', 'Your preferences have been updated successfully!');

            return $this->redirectToRoute('app_users_profile');
        }

        return $this->render('users/profile.html.twig', [
            'user' => $user,
            'settings' => $this->settingRepository->find(1),
            'userImagesForm' => $userImagesForm->createView(),
        ]);
    }
}
