<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\PreferenceRepository;
use App\Repository\WidgetRepository;
use App\Service\PinService;
use App\Validator\PreferenceValidator;
use App\Validator\WidgetToggleValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/me/preferences', name: 'api_me_')]
#[IsGranted('ROLE_USER')]
final class PreferenceController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PreferenceRepository $preferenceRepository,
        private PinService $pinService,
        private PreferenceValidator $preferenceValidator,
        private WidgetToggleValidator $widgetToggleValidator,
        private WidgetRepository $widgetRepository
    ) {
    }

    #[Route('', name: 'my_preferences', methods: ['GET'])]
    public function getPreferences(#[CurrentUser] ?User $user, Request $request): Response
    {
        $preferences = $this->preferenceRepository->findOneBy(['user' => $user]);

        return $this->json($preferences, Response::HTTP_OK, [], ['groups' => 'preferences:info']);
    }

    #[Route('/pin', name: 'pin_app', methods: ['PATCH'])]
    public function pinApp(#[CurrentUser] ?User $user, Request $request): Response
    {
        if (! $user instanceof \App\Entity\User) {
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

    #[Route('/widgets', name: 'update_widgets', methods: ['PATCH'])]
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

    #[Route('/{preferenceKey}', name: 'update_preference', methods: ['PATCH'])]
    public function updatePreference(
        #[CurrentUser]
        ?User $user,
        Request $request,
        string $preferenceKey
    ): Response {
        try {
            $this->preferenceValidator->validate($preferenceKey, $request);

            $data = json_decode($request->getContent(), true);
            if (! $this->preferenceValidator->isAllowedValue($preferenceKey, $data[$preferenceKey])) {
                throw new \InvalidArgumentException(sprintf('Invalid value for %s', $preferenceKey));
            }

            $this->updateUserPreference($user, $preferenceKey, $data[$preferenceKey]);
            $this->entityManager->flush();

            $message = sprintf("Preference '%s' updated", $preferenceKey);

            return $this->json(['message' => $message], Response::HTTP_OK);
        } catch (\InvalidArgumentException $invalidArgumentException) {
            return $this->json(['message' => $invalidArgumentException->getMessage()], Response::HTTP_BAD_REQUEST);
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
        $preference = $user->getPreference();
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
    private function updateUserPreference(
        User $user,
        string $preferenceKey,
        string $preferenceValue,
    ): void {
        $preferences = $user->getPreference();
        $setterMethod = 'set'.ucfirst($preferenceKey);
        if (! method_exists($preferences, $setterMethod)) {
            throw new \InvalidArgumentException('Invalid preference key: '.$preferenceKey);
        }

        $preferences->$setterMethod($preferenceValue);
    }
}
