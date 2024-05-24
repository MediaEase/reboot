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

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use sixlive\DotenvEditor\DotenvEditor;

#[AsCommand(
    name: 'secrets:regenerate-jwt-passphrase',
    description: 'Regenerate a random value and update JWT_PASSPHRASE',
)]
class RegenerateJwtPassphraseCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('envfile', InputArgument::REQUIRED, 'env File {.env, .env.local}');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        $envname = $input->getArgument('envfile');

        if ($envname && ($envname == '.env' || $envname == '.env.local')) {
            $symfonyStyle->note(sprintf('You chose to update: %s', $envname));
            $secret = bin2hex(random_bytes(32));
            $filepath = realpath(dirname(__FILE__).'/../..').'/'.$envname;
            $symfonyStyle->note(sprintf('Editing file: %s', $filepath));

            $dotenvEditor = new DotenvEditor();
            $dotenvEditor->load($filepath);
            $dotenvEditor->set('JWT_PASSPHRASE', $secret);
            $dotenvEditor->save();
            $symfonyStyle->success('New JWT_PASSPHRASE was generated: '.$secret);

            return Command::SUCCESS;
        }

        $symfonyStyle->error('You did not provide a valid environment file to change');

        return Command::INVALID;
    }
}
