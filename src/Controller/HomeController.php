<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\PreferenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class HomeController extends AbstractController
{
    public function __construct(
        private PreferenceRepository $preferenceRepository,
    ) {
    }

    #[Route('/', name: 'app_home')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        $user = $this->getUser();
        $preferences = $this->preferenceRepository->findOneBy(['user' => $user]);

        return $this->render('dashboard/index.html.twig', [
            'user' => $user,
            'preferences' => $preferences,
        ]);
    }
}
