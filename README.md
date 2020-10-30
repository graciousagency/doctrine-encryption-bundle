# Gracious Doctrine Encryption Bundle

Simple bundle to add 2 new types to Doctrine
* encrypted
* encryptedArrayCollection
* hashed

It relies on libSodium for encryption

## Installation
The Installation is quite simple: 

1. Require the Bundle via composer:
```text
composer require gracious/doctrine-encryption-bundle
```
2. Add the following to your doctrine.yaml:
```yaml
types:
  encrypted: 'Gracious\DoctrineEncryptionBundle\Type\Encrypted'
  encryptedArrayCollection: 'Gracious\DoctrineEncryptionBundle\Type\EncryptedArrayCollection'
  hashed: 'Gracious\DoctrineEncryptionBundle\Type\Hashed'
```
3. Generate a 64 character encryption key, you could to this the following way:
```php
sodium_bin2hex(random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES));
```
4. Add the following two settings to your .env file:
```text
ENABLE_ENCRYPTION=true
ENCRYPTION_KEY=[PASTE ENCRYPTION KEY HERE]
```

## Settings
There are 2 settings at the moment, both are env vars

* ENABLE_ENCRYPTION - true / false 

* ENCRYPTION_KEY - 64 character hexadecimal string

## Generating a key
You can do 2 things to generate a key, either type one yourself or run:

```php
sodium_bin2hex(random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES));
```

## Nonce
Nonces are automatically generated for each encrypted value and are added to the returned value as follows:

```text
<nonce|encrypted value>
```

## Doctrine settings
The following has to be added to you doctrine.yaml

```yaml
types:
  encrypted: 'Gracious\DoctrineEncryptionBundle\Type\Encrypted'
  encryptedArrayCollection: 'Gracious\DoctrineEncryptionBundle\Type\EncryptedArrayCollection'
  hashed: 'Gracious\DoctrineEncryptionBundle\Type\Hashed'
```
The block would look something like this:
```yaml
doctrine:
    dbal:
        # configure these for your database server
        driver: 'pdo_mysql'
        server_version: '5.7'
        charset: utf8mb4
        default_table_options:
            charset: utf8mb4
            collate: utf8mb4_unicode_ci

        # With Symfony 3.3, remove the `resolve:` prefix
        url: '%env(resolve:DATABASE_URL)%'
        types:
          encrypted: 'Gracious\DoctrineEncryptionBundle\Type\Encrypted'
          encryptedArrayCollection: 'Gracious\DoctrineEncryptionBundle\Type\EncryptedArrayCollection'
          hashed: 'Gracious\DoctrineEncryptionBundle\Type\Hashed'
```

## Usage
To use either of the 3 types in your entity just replace the column type with
```text
@ORM\Column(type="encrypted")
```
or
```text
@ORM\Column(type="encryptedArrayCollection")
```
or
```text
@ORM\Column(type="hashed")
```
