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
    name: 'ip:decrypt',
    description: 'Decrypts a given string',
)]
final class IPDecryptorCommand extends Command
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
            ->setDescription('Decrypts a given string')
            ->addArgument('data', InputArgument::REQUIRED, 'The data to decrypt');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        $data = $input->getArgument('data');
        $decryptedData = $this->decrypt($data);
        $symfonyStyle->success('Decrypted data: ' . $decryptedData);

        return Command::SUCCESS;
    }

    private function decrypt($data): string|false
    {
        $data = base64_decode($data, true);
        $iv = substr($data, 0, $this->ivLength);
        $encryptedData = substr($data, $this->ivLength);

        return openssl_decrypt($encryptedData, 'aes-256-cbc', $this->key, 0, $iv);
    }
}
