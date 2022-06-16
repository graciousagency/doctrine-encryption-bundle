<?php

namespace Gracious\DoctrineEncryptionBundle\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class Encrypted extends Type
{

    const ENCRYPTED = 'encrypted';

    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param array $fieldDeclaration The field declaration.
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The currently used database platform.
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'TEXT COMMENT \'(DC2Type:encrypted)\'';
    }

    /**
     * Gets the name of this type.
     *
     * @return string
     */
    public function getName()
    {
        return self::ENCRYPTED;
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return string
     */
    public function convertToPHPValue($value, AbstractPlatform $platform) : string
    {
        if (empty($value)) {
            return '';
        }

        if (!isset($_ENV['ENABLE_ENCRYPTION']) || $_ENV['ENABLE_ENCRYPTION'] === 'false') {
            return $value;
        }

        [$nonce, $encryptedValue] = explode('|', $value);
        return sodium_crypto_secretbox_open(sodium_hex2bin($encryptedValue), sodium_hex2bin($nonce), sodium_hex2bin($_ENV['ENCRYPTION_KEY']));
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return mixed
     * @throws \Exception
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!isset($_ENV['ENABLE_ENCRYPTION']) || $_ENV['ENABLE_ENCRYPTION'] === 'false') {
            return $value;
        }

        if (empty($value)) {
            return '';
        }

        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $key = sodium_hex2bin($_ENV['ENCRYPTION_KEY']);

        $encryptedValue = sodium_crypto_secretbox($value, $nonce, $key);
        return sodium_bin2hex($nonce).'|'.sodium_bin2hex($encryptedValue);
    }
}
