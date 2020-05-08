<?php

namespace Gracious\DoctrineEncryptionBundle\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class EncryptedArrayCollection extends Type
{

    const ENCRYPTEDARRAYCOLLECTION = 'encryptedArrayCollection';

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
        return 'LONGTEXT COMMENT \'(EncryptedArrayCollection)\'';
    }

    /**
     * Gets the name of this type.
     *
     * @return string
     */
    public function getName()
    {
        return self::ENCRYPTEDARRAYCOLLECTION;
    }

    /**
     * @param array $array
     * @param AbstractPlatform $platform
     * @return array
     */
    public function convertToPHPValue($array, AbstractPlatform $platform): array
    {
        $array = json_decode($array, true);

        if (empty($array)) {
            return [''];
        }

        if (!isset($_ENV['ENABLE_ENCRYPTION']) || $_ENV['ENABLE_ENCRYPTION'] === 'false') {
            return $array;
        }

        $decryptedArray = [];

        foreach ($array as $index => $value) {
            list($nonce, $encryptedValue) = explode('|', $value);
            $decryptedValue = sodium_crypto_secretbox_open(sodium_hex2bin($encryptedValue), sodium_hex2bin($nonce), sodium_hex2bin($_ENV['ENCRYPTION_KEY']));

            $decryptedArray[$index] = $decryptedValue;
        }

        return $decryptedArray;
    }

    /**
     * @param array $array
     * @param AbstractPlatform $platform
     * @return mixed
     * @throws \Exception
     */
    public function convertToDatabaseValue($array, AbstractPlatform $platform)
    {
        if (!isset($_ENV['ENABLE_ENCRYPTION']) || $_ENV['ENABLE_ENCRYPTION'] === 'false') {
            return $array;
        }

        if (empty($array)) {
            return [''];
        }

        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $key = sodium_hex2bin($_ENV['ENCRYPTION_KEY']);

        $encryptedArray = [];

        foreach ($array as $index => $value) {
            $encryptedValue = sodium_crypto_secretbox($value, $nonce, $key);
            $gluedEncryptedValue = sodium_bin2hex($nonce) . '|' . sodium_bin2hex($encryptedValue);

            $encryptedArray[$index] = $gluedEncryptedValue;
        }

        return json_encode($encryptedArray);
    }
}
