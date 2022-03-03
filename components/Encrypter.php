<?php

/**
 * Contains the component used for encrypting and decrypting data.
 *
 * @copyright Copyright (c) 2022 Dmytro Bereznii
 * @package bereznii/yii2-encrypter
 * @author Dmytro Bereznii <bereznii.d@gmail.com>
 */

namespace bereznii\encrypter\components;

use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * Encrypter is the class that is used to encrypt and decrypt the data.
 *
 * @author Dmytro Bereznii <bereznii.d@gmail.com>
 * @version 1.2
 */
class Encrypter extends Component
{
    private const AES128 = 'aes-128-cbc';
    private const AES256 = 'aes-256-cbc';
    public const IV_LENGTH = 16;
    public const KEY_LENGTH = 32;

    /** @var string $_key Contains the global password used to encrypt and decrypt. */
    private $_key;

    /**
     * Checks that the key and iv have indeed been set.
     *
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if (!$this->_key) {
            throw new InvalidConfigException('"' . get_class($this) . '::key" cannot be null.');
        }
    }

    /**
     * Sets the global password for the encrypter.
     *
     * @param string $key the global password
     * @throws InvalidConfigException
     */
    public function setKey(string $key)
    {
        $trimmedValue = trim($key);

        if (empty($trimmedValue)) {
            throw new InvalidConfigException('"' . get_class($this) . '::key length should be greater than 0.');
        }

        if (strlen($trimmedValue) > self::KEY_LENGTH) {
            throw new InvalidConfigException('"' . get_class($this) . '::key length should not be greater than 32.');
        }

        $this->_key = $trimmedValue;
    }

    /**
     * Get a pseudo-random string of bytes as initialization vector.
     *
     * @return false|string
     */
    public function getIv()
    {
        return openssl_random_pseudo_bytes(self::IV_LENGTH);
    }

    /**
     * @param string $string the string to encrypt
     * @return string the encrypted string
     */
    public function encrypt(string $string): string
    {
        $iv = $this->getIv();
        $encryptedString = openssl_encrypt($string, $this->getCypherMethod(), $this->_key, true, $iv);

        return base64_encode("{$iv}{$encryptedString}");
    }

    /**
     * False is returned in case it was not possible to decrypt it.
     *
     * @param string $string the string to decrypt
     * @return string|bool the decrypted string
     */
    public function decrypt(string $string)
    {
        try {
            $decodedString = base64_decode($string);

            $extractedIv = substr($decodedString, 0,self::IV_LENGTH);
            $encryptedString = substr($decodedString, self::IV_LENGTH);

            return openssl_decrypt($encryptedString, $this->getCypherMethod(), $this->_key, true, $extractedIv);
        } catch (\Exception $e) {
            Yii::error($e, YII_LOG_CATEGORY);
            return false;
        }
    }

    /**
     * Returns the cypher method based on the current configuration.
     *
     * @return string the cypher method
     */
    private function getCypherMethod(): string
    {
        return self::AES256;
    }
}
