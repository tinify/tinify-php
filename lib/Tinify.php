<?php

namespace Tinify;

const VERSION = "1.6.4";

class Tinify {
    /**
     * @var string|null API key.
     */
    private static $key = NULL;

    /**
     * @var string|null Identifier used for requests.
     */
    private static $appIdentifier = NULL;

    /**
     * @var string|null URL to the compression API.
     */
    private static $proxy = NULL;

    /**
     * @var int|null The number of compressions.
     */
    private static $compressionCount = NULL;

    /**
     * @var Client|null Tinify client.
     */
    private static $client = NULL;

    /**
     * Sets the key and resets the client.
     *
     * @param string $key The API key.
     * @return void
     */
    public static function setKey($key) {
        self::$key = $key;
        self::$client = NULL;
    }

    /**
     * Sets the app identifier and resets the client.
     *
     * @param string $appIdentifier The app identifier.
     * @return void
     */
    public static function setAppIdentifier($appIdentifier) {
        self::$appIdentifier = $appIdentifier;
        self::$client = NULL;
    }

    /**
     * Sets the proxy and resets the client.
     *
     * @param string $proxy URL to the proxy server.
     * @return void
     */
    public static function setProxy($proxy) {
        self::$proxy = $proxy;
        self::$client = NULL;
    }

    /**
     * Retrieves the compression count.
     *
     * @return int|null
     */
    public static function getCompressionCount() {
        return self::$compressionCount;
    }

    /**
     * Sets the compression count
     *
     * @param int $compressionCount
     * @return void
     */
    public static function setCompressionCount($compressionCount) {
        self::$compressionCount = $compressionCount;
    }

    /**
     * Retrieves the tinify client.
     * Will initiate a new client with the current key, identifier and proxy.
     *
     * @return Client
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
     * Sets a new client
     *
     * @param Client $client
     * @return void
     */
    public static function setClient($client) {
        self::$client = $client;
    }
}

function setKey($key) {
    return Tinify::setKey($key);
}

function setAppIdentifier($appIdentifier) {
    return Tinify::setAppIdentifier($appIdentifier);
}

function setProxy($proxy) {
    return Tinify::setProxy($proxy);
}

function getCompressionCount() {
    return Tinify::getCompressionCount();
}

function compressionCount() {
    return Tinify::getCompressionCount();
}

function fromFile($path) {
    return Source::fromFile($path);
}

function fromBuffer($string) {
    return Source::fromBuffer($string);
}

function fromUrl($string) {
    return Source::fromUrl($string);
}

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
