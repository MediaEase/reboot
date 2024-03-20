<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\SmtpSettingsType;
use App\Updater\DotenvUpdater;
use App\Form\GeneralSettingType;
use App\Repository\ServiceRepository;
use App\Repository\SettingRepository;
use App\Security\SecretManager;
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
        private DotenvUpdater $dotenvUpdater,
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
        $this->serviceRepository->findAll();

        return $this->render('settings/subdomains.html.twig');
    }

    #[Route('/system/smtp', name: 'system_smtp')]
    #[IsGranted('ROLE_ADMIN')]
    public function systemEmailsSettings(Request $request, SecretManager $secretManager): \Symfony\Component\HttpFoundation\Response
    {
        $user = $this->getUser();
        $defaultData = [
            'mail_protocol' => $this->getParameter('app.mail_protocol'),
            'mail_parameters' => $this->getParameter('app.mail_parameters'),
            'smtp_hostname' => $secretManager->getSecret('SMTP_HOSTNAME'),
            'smtp_username' => $secretManager->getSecret('SMTP_USERNAME'),
            'smtp_password' => $secretManager->getSecret('SMTP_PASSWORD'),
            'smtp_port' => $secretManager->getSecret('SMTP_PORT'),
            'smtp_timeout' => $this->getParameter('app.smtp_timeout'),
        ];
        $form = $this->createForm(SmtpSettingsType::class, $defaultData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $envFilePath = $this->containerBag->get('kernel.project_dir').'/.env.local';
            $this->dotenvUpdater->updateEnvFile($formData, $envFilePath);
        }

        return $this->render('settings/smtp.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'mailer_dsn' => $secretManager->getSecret('MAILER_DSN'),
        ]);
    }

    #[Route('/system/help', name: 'system_help')]
    #[IsGranted('ROLE_ADMIN')]
    public function systemHelpSettings(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('settings/help.html.twig');
    }
}
