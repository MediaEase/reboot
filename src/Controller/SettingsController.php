<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\SmtpSettingsType;
use App\Form\GeneralSettingType;
use App\Repository\ServiceRepository;
use App\Repository\SettingRepository;
use App\Repository\PreferenceRepository;
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
        private ServiceRepository $serviceRepository,
        private PreferenceRepository $preferenceRepository,
    ) {
    }

    #[Route('/', name: 'settings')]
    public function redirectToGeneral(): RedirectResponse
    {
        return $this->redirectToRoute('app_settings_general');
    }

    #[Route('/general', name: 'general')]
    public function generalSettings(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $settings = $this->settingRepository->findLast();
        $user = $this->getUser();
        $preferences = $this->preferenceRepository->findOneBy(['user' => $user]);
        $form = $this->createForm(GeneralSettingType::class, $settings);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($settings);
            $this->entityManager->flush();
        }

        return $this->render('settings/general.html.twig', [
            'settings' => $settings,
            'user' => $user,
            'preferences' => $preferences,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/system/subdomains', name: 'system_subdomains')]
    #[IsGranted('ROLE_ADMIN')]
    public function systemSubdomainsSettings(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $services = $this->serviceRepository->findAll();
        dd($services);
        return $this->render('settings/subdomains.html.twig');
    }

    #[Route('/system/smtp', name: 'system_smtp')]
    #[IsGranted('ROLE_ADMIN')]
    public function systemEmailsSettings(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $defaultData = [];
        $form = $this->createForm(SmtpSettingsType::class, $defaultData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $this->updateEnvFile($formData);
        }

        return $this->render('settings/smtp.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/system/help', name: 'system_help')]
    #[IsGranted('ROLE_ADMIN')]
    public function systemHelpSettings(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('settings/help.html.twig');
    }

    private function updateEnvFile(array $formData): void
    {
        $envPath = $this->containerBag->get('kernel.project_dir').'/.env.local';
        $content = file_get_contents($envPath);

        foreach ($formData as $key => $value) {
            $content = preg_replace(
                '/^'.$key.'=.*$/m',
                $key.'='.$value,
                $content
            );
        }

        file_put_contents($envPath, $content);
    }
}
