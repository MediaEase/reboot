<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\GeneralSettingFormType;
use App\Repository\ServiceRepository;
use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

#[Route('/settings', name: 'app_settings_')]
#[IsGranted('ROLE_USER')]
final class SettingsController extends AbstractController
{
    public function __construct(
        private ContainerBagInterface $containerBag, 
        private SettingRepository $settingRepository, 
        private EntityManagerInterface $entityManager, 
        private ServiceRepository $serviceRepository)
    {
    }

    #[Route('/general', name: 'general')]
    public function generalSettings(Request $request)
    {
        $settings = $this->settingRepository->findLast();
        $user = $this->getUser();
        $form = $this->createForm(GeneralSettingFormType::class, $settings);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($settings);
            $this->entityManager->flush();
        }

        return $this->render('settings/general.html.twig', [
            'settings' => $settings,
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/security', name: 'security')]
    public function securitySettings(Request $request)
    {
    }

    #[Route('/session', name: 'session')]
    public function sessionSettings(Request $request)
    {
    }

    #[Route('/users', name: 'users')]
    public function usersSettings(Request $request)
    {
    }

    #[Route('/users/groups', name: 'users_groups')]
    public function groupSettings(Request $request)
    {
    }

    #[Route('/users/manage', name: 'users_manage')]
    public function usersManageSettings(Request $request)
    {
    }

    #[Route('/users/registration', name: 'users_registration')]
    public function usersRegistrationSettings(Request $request)
    {
    }

    #[Route('/system/api', name: 'system_api')]
    public function systemApiSettings(Request $request)
    {
    }

    #[Route('/system/help', name: 'system_help')]
    public function systemHelpSettings(Request $request)
    {
    }

    #[Route('/system/logs', name: 'system_logs')]
    public function systemLogsSettings(Request $request)
    {
    }

    #[Route('/system/subdomains', name: 'system_subdomains')]
    #[IsGranted('ROLE_ADMIN')]
    public function systemSubdomainsSettings(Request $request)
    {
        $services = $this->serviceRepository->findAll();
        dd($services);
    }

    #[Route('/system/troobleshoot', name: 'system_troobleshoot')]
    public function systemTroobleshootSettings(Request $request)
    {
    }

    #[Route('/system/update', name: 'system_update')]
    public function systemUpdateSettings(Request $request)
    {
    }

    #[Route('/', name: 'settings')]
    public function redirectToGeneral(): RedirectResponse
    {
        return $this->redirectToRoute('app_settings_general');
    }
}
