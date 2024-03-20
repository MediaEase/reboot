<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use sixlive\DotenvEditor\DotenvEditor;

#[AsCommand(
    name: 'secrets:regenerate-app-secret',
    description: 'Regenerate a random value and update APP_SECRET',
)]
class RegenerateAppSecretCommand extends Command
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
            $secret = bin2hex(random_bytes(16));
            $filepath = realpath(dirname(__FILE__).'/../..').'/'.$envname;
            $symfonyStyle->note(sprintf('Editing file: %s', $filepath));

            $dotenvEditor = new DotenvEditor();
            $dotenvEditor->load($filepath);
            $dotenvEditor->set('APP_SECRET', $secret);
            $dotenvEditor->save();
            $symfonyStyle->success('New APP_SECRET was generated: '.$secret);

            return Command::SUCCESS;
        }

        $symfonyStyle->error('You did not provide a valid environment file to change');

        return Command::INVALID;
    }
}
