<?php

declare(strict_types=1);

namespace KsPlayer\Security;

class Encryption
{
    private string $secretKey;

    public function __construct(?string $secretKey = null)
    {
        $this->secretKey = $secretKey ?? (defined('SECRET_KEY') ? SECRET_KEY : '');
    }

    public function decryptPostId(string $cipherText): ?string
    {
        $decoded = base64_decode(str_replace(['-', '_'], ['+', '/'], $cipherText));
        if (!$decoded || strlen($decoded) < 29) {
            return null;
        }

        $hashKey = hash('sha256', $this->secretKey, true);
        $initializationVector = substr($decoded, 0, 12);
        $authenticationTag = substr($decoded, 12, 16);
        $encryptedData = substr($decoded, 28);

        $decryptedData = openssl_decrypt(
            $encryptedData,
            'aes-256-gcm',
            $hashKey,
            OPENSSL_RAW_DATA,
            $initializationVector,
            $authenticationTag
        );

        return $decryptedData ?: null;
    }

    public function encryptSubtitleContent(string $content, string $key): string
    {
        $cipherKey = hash('sha256', $key, true);
        $initializationVector = openssl_random_pseudo_bytes(16);

        $encryptedData = openssl_encrypt(
            $content,
            'aes-256-cbc',
            $cipherKey,
            OPENSSL_RAW_DATA,
            $initializationVector
        );

        return base64_encode($initializationVector . $encryptedData);
    }
}