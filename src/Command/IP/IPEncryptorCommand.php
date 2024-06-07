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

namespace App\Command\IP;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'ip:encrypt',
    description: 'Encrypts a given string',
)]
final class IPEncryptorCommand extends Command
{
    private bool|string|int|float|\UnitEnum|array|null $key;

    private int|bool $ivLength;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->key = $parameterBag->get('app.encryption_key');
        $this->ivLength = openssl_cipher_iv_length('aes-256-cbc');
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Encrypts a given string')
            ->addArgument('data', InputArgument::REQUIRED, 'The data to encrypt');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        $data = $input->getArgument('data');
        $encryptedData = $this->encrypt($data);
        $symfonyStyle->success('Encrypted data: ' . $encryptedData);

        return Command::SUCCESS;
    }

    private function encrypt($data): string
    {
        $iv = openssl_random_pseudo_bytes($this->ivLength);
        $encryptedData = openssl_encrypt($data, 'aes-256-cbc', $this->key, 0, $iv);

        return base64_encode($iv.$encryptedData);
    }
}
