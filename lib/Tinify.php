<?php

namespace Tinify;

const VERSION = "1.3.0";

class Tinify {
    private static $key = NULL;
    private static $appIdentifier = NULL;
    private static $compressionCount = NULL;
    private static $client = NULL;
    private static $requestTimeout = -1;
    private static $requestTimeoutMS = -1;
    private static $connectionTimeout = -1;
    private static $connectionTimeoutMS = -1;

    public static function setKey($key) {
        self::$key = $key;
        self::$client = NULL;
    }

    public static function setAppIdentifier($appIdentifier) {
        self::$appIdentifier = $appIdentifier;
        self::$client = NULL;
    }

    public static function getCompressionCount() {
        return self::$compressionCount;
    }

    public static function setCompressionCount($compressionCount) {
        self::$compressionCount = $compressionCount;
    }

    public static function getClient() {
        if (!self::$key) {
            throw new AccountException("Provide an API key with Tinify\setKey(...)");
        }

        if (!self::$client) {
            self::$client = new Client(self::$key, self::$appIdentifier);
            if (self::$requestTimeout > -1) {
                self::$client->setRequestTimeout(self::$requestTimeout);
            }
            if (self::$requestTimeoutMS > -1) {
                self::$client->setRequestTimeoutMS(self::$requestTimeoutMS);
            }
            if (self::$connectionTimeout > -1) {
                self::$client->setConnectionTimeout(self::$connectionTimeout);
            }
            if (self::$connectionTimeoutMS > -1) {
                self::$client->setConnectionTimeoutMS(self::$connectionTimeoutMS);
            }
        }

        return self::$client;
    }

    public static function setClient($client) {
        self::$client = $client;
    }

    public static function setRequestTimeout($seconds)
    {
        self::$requestTimeout = $seconds;
    }

    public static function setRequestTimeoutMS($milliseconds)
    {
        self::$requestTimeoutMS = $milliseconds;
    }

    public static function setConnectionTimeout($seconds)
    {
        self::$connectionTimeout = $seconds;
    }

    public static function setConnectionTimeoutMS($milliseconds)
    {
        self::$connectionTimeoutMS = $milliseconds;
    }
}

function setKey($key) {
    return Tinify::setKey($key);
}

function setAppIdentifier($appIdentifier) {
    return Tinify::setAppIdentifier($appIdentifier);
}

function getCompressionCount() {
    return Tinify::getCompressionCount();
}

function compressionCount() {
    return Tinify::getCompressionCount();
}

function setRequestTimeout($seconds) {
    return Tinify::setRequestTimeout($seconds);
}

function setRequestTimeoutMS($milliseconds) {
    return Tinify::setRequestTimeoutMS($milliseconds);
}

function setConnectionTimeout($seconds) {
    return Tinify::setConnectionTimeout($seconds);
}

function setConnectionTimeoutMS($milliseconds) {
    return Tinify::setConnectionTimeoutMS($milliseconds);
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
