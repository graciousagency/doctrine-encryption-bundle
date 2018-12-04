# Gracious Doctrine Encryption Bundle

Simple bundle to add 2 new types to Doctrine
* encrypted
* hashed

It relies on libSodium for encyption

## Settings
There are 2 settings at the moment, both are env vars

* ENABLE_ENCRYPTION - true / false 

* ENCRYPTION_KEY - 64 character hexadecimal string

## Generating a key
You can do 2 things to generate a key, either type one yourself or run:

```php
random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
```

## Nonce
Nonces are automatically generated for each encrypted value and are added to the returned value as follows:

```text
<encrypted value|nonce>
```

## Doctrine settings
The following has to be added to you doctrine.yaml

```yaml
types:
  encrypted: 'App\Type\Encrypted'
  hashed: 'App\Type\Hashed'
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
          encrypted: 'App\Type\Encrypted'
          hashed: 'App\Type\Hashed'
```

## Usage
To use either of the 2 types in your entity just replace the column type with
```text
@ORM\Column(type="encrypted")
```
or
```text
@ORM\Column(type="hashed")
```
