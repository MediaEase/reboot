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

namespace App\Controller\Site\Admin;

use App\Form\Setting\PhpType;
use App\Form\Setting\SmtpType;
use App\Updater\DotenvUpdater;
use App\Updater\PhpIniUpdater;
use App\Security\SecretManager;
use App\Form\Setting\GeneralType;
use App\Repository\GroupRepository;
use App\Repository\StoreRepository;
use App\Repository\ServiceRepository;
use App\Repository\SettingRepository;
use Symfony\Component\Process\Process;
use App\Repository\PreferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Process\Exception\ProcessFailedException;
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
        private GroupRepository $groupRepository,
        private StoreRepository $storeRepository,
    ) {
    }

    #[Route('/', name: 'settings')]
    public function redirectToGeneral(): RedirectResponse
    {
        return $this->redirectToRoute('app_settings_general');
    }

    #[Route('/general', name: 'general')]
    public function generalSettings(Request $request): Response
    {
        $settings = $this->settingRepository->findLast();
        $user = $this->getUser();
        $preferences = $this->preferenceRepository->findOneBy(['user' => $user]);
        $interfaces = [];
        $interfacesFile = '/etc/.mediaease/interfaces';
        if (file_exists($interfacesFile)) {
            $fileContent = file($interfacesFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($fileContent as $line) {
                $interfaces[] = trim($line);
            }
        }

        $form = $this->createForm(GeneralType::class, $settings, ['interfaces' => $interfaces]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($settings);
            $this->entityManager->flush();
            $this->addFlash('success', 'The settings have been updated successfully.');
        }

        return $this->render('pages/settings/general.html.twig', [
            'user' => $user,
            'preferences' => $preferences,
            'form' => $form->createView(),
            'settings' => $settings,
        ]);
    }

    #[Route('/system/subdomains', name: 'system_subdomains')]
    #[IsGranted('ROLE_ADMIN')]
    public function systemSubdomainsSettings(Request $request): Response
    {
        $this->serviceRepository->findAll();

        return $this->render('pages/settings/subdomains.html.twig');
    }

    #[Route('/system/smtp', name: 'system_smtp')]
    #[IsGranted('ROLE_ADMIN')]
    public function systemEmailsSettings(Request $request, SecretManager $secretManager): Response
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
        $form = $this->createForm(SmtpType::class, $defaultData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $envFilePath = $this->containerBag->get('kernel.project_dir').'/.env.local';
            $this->dotenvUpdater->updateEnvFile($formData, $envFilePath);
            $this->addFlash('success', 'The SMTP settings have been updated successfully.');
            $this->reloadPhpServices();
        }

        return $this->render('pages/settings/smtp.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'mailer_dsn' => $secretManager->getSecret('MAILER_DSN'),
            'settings' => $this->settingRepository->find(1),
        ]);
    }

    #[Route('/system/help', name: 'system_help')]
    #[IsGranted('ROLE_ADMIN')]
    public function systemHelpSettings(Request $request): Response
    {
        return $this->render('pages/settings/help.html.twig');
    }

    #[Route('/system/php', name: 'system_php')]
    #[IsGranted('ROLE_ADMIN')]
    public function systemPhpSetting(Request $request, PhpIniUpdater $phpIniUpdater): Response
    {
        $settings = $this->settingRepository->findLast();
        $user = $this->getUser();
        $iniFilePath = '/etc/php/8.3/cli/conf.d/99-mediaease.ini';
        $defaultData = $phpIniUpdater->getIniConfig($iniFilePath);
        if (!is_array($defaultData)) {
            throw new \RuntimeException('Expected an array of configuration settings');
        }

        $form = $this->createForm(PhpType::class, $defaultData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $cliIniPath = '/etc/php/8.3/cli/conf.d/99-mediaease.ini';
            $fpmIniPath = '/etc/php/8.3/fpm/conf.d/99-mediaease.ini';
            $phpIniUpdater->updateIniFiles($formData, [$cliIniPath, $fpmIniPath]);
            $this->addFlash('success', 'The PHP settings have been updated successfully.');
            $this->reloadPhpServices();
        }

        return $this->render('pages/settings/php.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'settings' => $settings,
        ]);
    }

    private function reloadPhpServices(): void
    {
        $services = ['php8.3-fpm.service', 'php8.3-cli.service'];
        foreach ($services as $service) {
            $process = new Process(['systemctl', 'reload', $service]);
            $process->run();
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
        }
    }
}
