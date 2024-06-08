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

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * EncryptionService encrypts and decrypts strings.
 */
final class EncryptionService
{
    private bool|string|int|float|\UnitEnum|array|null $key;

    private int|bool $ivLength;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->key = $parameterBag->get('app.encryption_key');
        $this->ivLength = openssl_cipher_iv_length('aes-256-cbc');
    }

    /**
     * Encrypts a string.
     *
     * @param string $data The data to encrypt
     *
     * @return string The encrypted data
     */
    public function encrypt($data): string
    {
        $iv = openssl_random_pseudo_bytes($this->ivLength);
        $encryptedData = openssl_encrypt($data, 'aes-256-cbc', $this->key, 0, $iv);

        return base64_encode($iv.$encryptedData);
    }

    /**
     * Decrypts a string.
     *
     * @param string $data The data to decrypt
     *
     * @return string The decrypted data
     */
    public function decrypt($data): string|false
    {
        $data = base64_decode($data, true);
        $iv = substr($data, 0, $this->ivLength);
        $encryptedData = substr($data, $this->ivLength);

        return openssl_decrypt($encryptedData, 'aes-256-cbc', $this->key, 0, $iv);
    }
}
