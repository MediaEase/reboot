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
use App\Form\User\UserImageType;
use App\Form\User\UserPreferenceType;
use App\Form\User\ChangeUserPasswordType;
use App\Repository\SettingRepository;
use App\Repository\PreferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Image\HandleImageService;
use App\Service\FormHandlerService;
use App\Service\PathAccessService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * UserController handles user profile-related operations.
 */
#[Route(name: 'app_users_')]
class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SettingRepository $settingRepository,
        private PreferenceRepository $preferenceRepository,
        private HandleImageService $handleImageService,
        private FormHandlerService $formHandlerService,
        private PathAccessService $pathAccessService
    ) {
    }

    /**
     * Displays and processes the user profile page.
     */
    #[Route('/me', name: 'profile', methods: ['GET', 'POST'])]
    public function profile(Request $request): Response
    {
        $user = $this->getUser();
        $preferences = $this->preferenceRepository->findOneBy(['user' => $user]);

        $forms = $this->initializeForms($user, $preferences);

        foreach ($forms as $form) {
            $form['form']->handleRequest($request);
            if (!$form['form']->isSubmitted()) {
                continue;
            }

            if (!$form['form']->isValid()) {
                continue;
            }

            $response = $this->formHandlerService->handleFormSubmission($form['type'], $user, $preferences, $form['form']);
            $responseData = json_decode($response->getContent(), true);
            $this->addFlash($responseData['status'], $responseData['message']);

            return $this->redirectToRoute('app_users_profile');
        }

        return $this->render('users/profile.html.twig', [
            'user' => $user,
            'settings' => $this->settingRepository->find(1),
            'userImagesForm' => $forms['userImagesForm']['form']->createView(),
            'userPreferencesForm' => $forms['userPreferencesForm']['form']->createView(),
            'changeUserPassForm' => $forms['changeUserPassForm']['form']->createView(),
            'addPathForm' => $forms['addPathForm']['form']->createView(),
        ]);
    }

    /**
     * Exports user data.
     */
    #[Route('/me/export', name: 'profile_export', methods: ['GET'])]
    public function exportUserData(Request $request): Response
    {
        return new Response('User data exported successfully.');
    }

    /**
     * Initializes forms for user profile.
     *
     * @param UserInterface|null $user
     */
    private function initializeForms(?\Symfony\Component\Security\Core\User\UserInterface $user, mixed $preferences): array
    {
        return [
            'userImagesForm' => [
                'form' => $this->createForm(UserImageType::class, $preferences),
                'type' => 'userImages',
            ],
            'userPreferencesForm' => [
                'form' => $this->createForm(UserPreferenceType::class, $preferences),
                'type' => 'userPreferences',
            ],
            'changeUserPassForm' => [
                'form' => $this->createForm(ChangeUserPasswordType::class, $user),
                'type' => 'changeUserPass',
            ],
            'addPathForm' => [
                'form' => $this->createForm(MountType::class, new Mount()),
                'type' => 'addPath',
            ],
        ];
    }
}
