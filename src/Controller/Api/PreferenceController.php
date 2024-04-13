<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\User;
use App\Service\PinService;
use App\Repository\GroupRepository;
use App\Repository\WidgetRepository;
use App\Validator\PreferenceValidator;
use App\Repository\PreferenceRepository;
use App\Validator\WidgetToggleValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/me/preferences', name: 'api_me_preferences_')]
#[IsGranted('ROLE_USER')]
final class PreferenceController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private GroupRepository $groupRepository,
        private PreferenceRepository $preferenceRepository,
        private PinService $pinService,
        private PreferenceValidator $preferenceValidator,
        private WidgetToggleValidator $widgetToggleValidator,
        private WidgetRepository $widgetRepository
    ) {
    }

    #[Route('/pin', name: 'pin', methods: ['PATCH'])]
    public function pin(#[CurrentUser] ?User $user, Request $request): Response
    {
        if (! $user instanceof User) {
            return $this->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $serviceId = $data['service'] ?? null;

        try {
            $this->pinService->handlePinning($user, $serviceId);

            return $this->json(['message' => 'Pinned apps updated'], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/widgets', name: 'widgets_update', methods: ['PATCH'])]
    public function updateWidgets(#[CurrentUser] ?User $user, Request $request): Response
    {
        try {
            $requestedWidgets = $this->getRequestedWidgets($request);

            $widgetToggleValidator = new WidgetToggleValidator($this->widgetRepository);
            $widgetToggleValidator->validateRequestedWidgets($requestedWidgets);
            $widgetToggleValidator->validateWidgets($requestedWidgets);
            $widgetToggleValidator->validateWidgetSizeSum($requestedWidgets);

            $this->updateUserWidgets($user, $requestedWidgets);

            return $this->json(['message' => 'Widgets updated'], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return $this->createErrorResponse($exception->getMessage());
        }
    }

    #[Route('/{preferenceKey}', name: 'key_update', methods: ['PATCH'])]
    public function updateKey(
        #[CurrentUser] ?User $user,
        Request $request,
        string $preferenceKey
    ): Response {
        if (!$user instanceof User) {
            return $this->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $value = $request->query->get('value');
        if (null === $value) {
            return $this->json(['message' => 'Bad request: Missing expected data'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->preferenceValidator->validate($preferenceKey, $value);
            if (!$this->preferenceValidator->isAllowedValue($preferenceKey, $value)) {
                throw new \InvalidArgumentException(sprintf('Invalid value for %s', $preferenceKey));
            }

            $this->updateUserPreferences($user, $preferenceKey, $value);
            $this->entityManager->flush();

            return $this->json(['message' => sprintf("Preference '%s' updated", $preferenceKey)], Response::HTTP_OK);
        } catch (\InvalidArgumentException $invalidArgumentException) {
            return $this->json(['message' => $invalidArgumentException->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $exception) {
            return $this->json(['message' => 'Server error: '.$exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @return array<string>|null
     */
    private function getRequestedWidgets(Request $request): ?array
    {
        $data = json_decode($request->getContent(), true);
        $widgets = $data['widgets'] ?? null;

        if (is_array($widgets)) {
            $uniqueWidgets = array_unique($widgets);

            return array_values($uniqueWidgets);
        }

        return null;
    }

    /**
     * @param array<string> $widgets
     */
    private function updateUserWidgets(User $user, array $widgets): void
    {
        $preference = $user->getPreferences();
        $preference->setSelectedWidgets($widgets);

        $this->entityManager->persist($preference);
        $this->entityManager->flush();
    }

    private function createErrorResponse(string $message): JsonResponse
    {
        return $this->json(['message' => $message], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @param array<string> $preferenceValue
     */
    private function updateUserPreferences(
        User $user,
        string $preferenceKey,
        string $preferenceValue,
    ): void {
        $preferences = $this->preferenceRepository->findOneBy(['user' => $user->getId()]);
        $setterMethod = 'set'.ucfirst($preferenceKey);
        if (! method_exists($preferences, $setterMethod)) {
            throw new \InvalidArgumentException('Invalid preference key: '.$preferenceKey);
        }

        $preferences->$setterMethod($preferenceValue);
    }
}
