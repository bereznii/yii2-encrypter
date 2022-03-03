Openssl Encrypter for Yii2
======================
Openssl Encrypter for Yii2
Version 1.2

This extension is used for two-way encryption.
The cypher method used is **AES256**.

The main difference from [original package](https://github.com/nickcv-ln/yii2-encrypter) is usage of unique initialization vector for each operation. 
The main purpose is to provide additional level of security by randomization of resulting hash. 
Also, as it seems from threads on stackoverflow, publicly stored IV is a very bad practice. However, it is needed to decrypt value. 
Implemented idea is described below. 

Step-by-step **encryption**:
1. Passphrase (32 bytes length) is being stored as ENV constant;
2. Unique IV is randomly generated for each encryption operation by [openssl_random_pseudo_bytes()](https://www.php.net/manual/en/function.openssl-random-pseudo-bytes.php) and is presented as 16 bytes length string;
3. Input is encrypted with [openssl_encrypt()](https://www.php.net/manual/en/function.openssl-encrypt.php). Result is encrypted byte-string;
4. IV (16 bytes) is prepended in front of byte-string from previous step;
5. Concatenated string is encoded with [base64_encode()](https://www.php.net/manual/en/function.base64-encode) to avoid encoding problems when transferring over a network or storing in a database.
6. Result is a securely encrypted and encoded string.

Step-by-step **decryption**:
1. Fully encrypted string is decoded with [base64_decode()](https://www.php.net/manual/en/function.base64-decode.php);
2. First 16 bytes is retrieved for further decryption process. This is IV;
3. Remaining part of string is encrypted with [openssl_decrypt()](https://www.php.net/manual/en/function.openssl-decrypt) using IV from previous step and passphrase from ENV;
4. Result is the initial string.

Openssl has been used in place of mcrypt because of its sheer speed in the encryption and decryption process (**up to 30 times faster**).

_______________________

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require bereznii/yii2-encrypter
```

or add

```
"bereznii/yii2-encrypter": "*"
```

to the require section of your `composer.json` file.

Set Up
------

To use Encrypter component it needs to be registered in [Components list of Yii2 Application](https://www.yiiframework.com/doc/guide/2.0/en/structure-application-components).

```
'components' => [
    'encrypter' => [
        'class' => \bereznii\encrypter\components\Encrypter::class,
        'key' => getenv('ENCRYPTION_KEY'),
    ],
    ...
]
```

## Basic Usage

### Component

Encrypter can be used both from web and console.

To encrypt value manually in any part of the application encrypter can be used as follows:

```
Yii::$app->encrypter->encrypt('Hello World!');
```

or to decrypt:

```
Yii::$app->encrypter->decrypt('Hello World!');
```

### Behavior

The extension also comes with a behavior class that can be easily attached to any ActiveRecord Model.

Use the following syntax to attach the behavior.

```
public function behaviors()
{
    return [
        'encryption' => [
            'class' => \bereznii\encrypter\behaviors\EncryptionBehavior::class,
            'attributes' => [
                'attributeName1',
                'attributeName2',
            ],
        ],
    ];
}
```

The behavior will automatically encrypt all the data before saving it on the database and decrypt it after the retrieve.

**Keep in mind that the behavior will use the current configuration of the extension for the encryption.**

Unit Testing
------------

[Original package](https://github.com/nickcv-ln/yii2-encrypter) was built with TDD. However, current package is not covered with
unit-tests due to lack of time caused by russian invasion to Ukraine. 
Hopefully, one day unit tests will be added and package will become more customizable. But until then, it is published for educational purposes only.

Warnings
--------

It is extremely hard (or practically impossible) to decrypt the data without the password, copy of it should be store in secure place to avoid losing all encrypted data.

**Two-way encryption should not be used to store passwords: you should use a one-way encryption function like sha1 and a SALT**