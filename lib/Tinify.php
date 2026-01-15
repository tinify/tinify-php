<?php

namespace Tinify;

const VERSION = "1.6.4";

class Tinify {
    private static $key = NULL;
    private static $appIdentifier = NULL;
    private static $proxy = NULL;

    private static $compressionCount = NULL;
    private static $client = NULL;

    /**
     * @param string $key
     * @return void
     */
    public static function setKey($key) {
        self::$key = $key;
        self::$client = NULL;
    }

    /**
     * @param string $appIdentifier
     * @return void
     */
    public static function setAppIdentifier($appIdentifier) {
        self::$appIdentifier = $appIdentifier;
        self::$client = NULL;
    }

    /**
     * @param string $proxy
     * @return void
     */
    public static function setProxy($proxy) {
        self::$proxy = $proxy;
        self::$client = NULL;
    }

    /**
     * @return int|null
     */
    public static function getCompressionCount() {
        return self::$compressionCount;
    }

    /**
     * @param int $compressionCount
     * @return void
     */
    public static function setCompressionCount($compressionCount) {
        self::$compressionCount = $compressionCount;
    }

    /**
     * @return Client
     * @throws AccountException
     */
    public static function getClient() {
        if (!self::$key) {
            throw new AccountException("Provide an API key with Tinify\setKey(...)");
        }

        if (!self::$client) {
            self::$client = new Client(self::$key, self::$appIdentifier, self::$proxy);
        }

        return self::$client;
    }

    /**
     * @param Client $client
     * @return void
     */
    public static function setClient($client) {
        self::$client = $client;
    }
}

/**
 * @param string $key
 * @return void
 */
function setKey($key) {
    return Tinify::setKey($key);
}

/**
 * @param string $appIdentifier
 * @return void
 */
function setAppIdentifier($appIdentifier) {
    return Tinify::setAppIdentifier($appIdentifier);
}

/**
 * @param string $proxy
 * @return void
 */
function setProxy($proxy) {
    return Tinify::setProxy($proxy);
}

/**
 * @return int|null
 */
function getCompressionCount() {
    return Tinify::getCompressionCount();
}

/**
 * @return int|null
 */
function compressionCount() {
    return Tinify::getCompressionCount();
}

/**
 * @param string $path
 * @return Source
 * @throws AccountException
 * @throws ClientException
 * @throws ServerException
 * @throws ConnectionException
 */
function fromFile($path) {
    return Source::fromFile($path);
}

/**
 * @param string $string
 * @return Source
 * @throws AccountException
 * @throws ClientException
 * @throws ServerException
 * @throws ConnectionException
 */
function fromBuffer($string) {
    return Source::fromBuffer($string);
}

/**
 * @param string $string
 * @return Source
 * @throws AccountException
 * @throws ClientException
 * @throws ServerException
 * @throws ConnectionException
 */
function fromUrl($string) {
    return Source::fromUrl($string);
}

/**
 * @return bool
 * @throws AccountException
 * @throws ServerException
 * @throws ConnectionException
 */
function validate() {
    try {
        Tinify::getClient()->request("post", "/shrink");
    } catch (AccountException $err) {
        if ($err->status == 429) return true;
        throw $err;
    } catch (ClientException $err) {
        return true;
    }
}
