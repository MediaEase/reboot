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

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Group;

#[AsCommand(
    name: 'create:base-groups',
    description: 'Create the MediaEase groups in the system',
)]
class CreateBaseGroups extends Command
{
    public function __construct(private EntityManagerInterface $entityManager, private UserPasswordHasherInterface $userPasswordHasher)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Creates the base groups in the system');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $installLockFile = './.mediaease-install.lock';
        if (!file_exists($installLockFile)) {
            $output->writeln('Error: The installation lock file does not exist. Command aborted.');

            return Command::FAILURE;
        }

        $groups = [
            'full',
            'automation',
            'media',
            'remote',
        ];
        foreach ($groups as $name) {
            $group = new Group();
            $group->setName($name);
            $this->entityManager->persist($group);
        }

        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
