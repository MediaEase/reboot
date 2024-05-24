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

namespace App\Command;

use App\Entity\User;
use App\Entity\Mount;
use App\Entity\Preference;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Group;

#[AsCommand(
    name: 'create:first-user',
    description: 'Create the first user in the system',
)]
class CreateFirstUserCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager, private UserPasswordHasherInterface $userPasswordHasher)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Creates a new admin user with provided arguments')
            ->addArgument('username', InputArgument::REQUIRED, 'Admin username')
            ->addArgument('password', InputArgument::REQUIRED, 'Admin password')
            ->addArgument('email', InputArgument::REQUIRED, 'Admin email')
            ->addArgument('is_verified', InputArgument::REQUIRED, 'Email verification status')
            ->addArgument('theme', InputArgument::REQUIRED, 'User theme')
            ->addArgument('mount', InputArgument::REQUIRED, 'User mount path')
            ->addArgument('shell', InputArgument::REQUIRED, 'User shell')
            ->addArgument('display', InputArgument::REQUIRED, 'User display preference');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $installLockFile = './.mediaease-install.lock';
        if (!file_exists($installLockFile)) {
            $output->writeln('Error: The installation lock file does not exist. Command aborted.');

            return Command::FAILURE;
        }

        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $email = $input->getArgument('email');
        $isVerified = $input->getArgument('is_verified') === true;
        $theme = $input->getArgument('theme');
        $mountPath = $input->getArgument('mount');
        $shell = $input->getArgument('shell');
        $display = $input->getArgument('display');

        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setGroup($this->entityManager->getRepository(Group::class)->findOneBy(['name' => 'full']));
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));
        $user->setIsVerified($isVerified);
        $user->setApiKey(bin2hex(random_bytes(16)));

        $this->entityManager->persist($user);

        $preference = new Preference();
        $preference->setUser($user);
        $preference->setTheme($theme);
        $preference->setSelectedWidgets(['cpu_1', 'mem_1', 'disk_1', 'net_3']);
        $preference->setPinnedApps([]);
        $preference->setDisplay($display);
        $preference->setShell($shell);

        $this->entityManager->persist($preference);

        $mount = new Mount();
        $mount->setPath($mountPath);
        $mount->setIsRclone(false);
        $mount->setUser($user);

        $this->entityManager->persist($mount);

        $this->entityManager->flush();

        $output->writeln('Admin user created successfully!');

        return Command::SUCCESS;
    }
}
