<?php

namespace Tinify;

const VERSION = "1.6.2";

class Tinify {
    const AUTHENTICATED = true;
    const ANONYMOUS = false;

    private static $key = NULL;
    private static $appIdentifier = NULL;
    private static $proxy = NULL;

    private static $compressionCount = NULL;
    private static $remainingCredits = NULL;
    private static $payingState = NULL;
    private static $emailAddress = NULL;

    private static $client = NULL;

    public static function setKey($key) {
        self::$key = $key;
        self::$client = NULL;
    }

    public static function getKey() {
        return self::$key;
    }

    public static function createKey($email, $options) {
        $body = array_merge(array("email" => $email), $options);
        $response = self::getClient(self::ANONYMOUS)->request("post", "/keys", $body);
        self::setKey($response->body->key);
    }

    public static function setAppIdentifier($appIdentifier) {
        self::$appIdentifier = $appIdentifier;
        self::$client = NULL;
    }

    public static function setProxy($proxy) {
        self::$proxy = $proxy;
        self::$client = NULL;
    }

    public static function getCompressionCount() {
        return self::$compressionCount;
    }

    public static function setCompressionCount($compressionCount) {
        self::$compressionCount = $compressionCount;
    }

    public static function getRemainingCredits() {
        return self::$remainingCredits;
    }

    public static function setRemainingCredits($remainingCredits) {
        self::$remainingCredits = $remainingCredits;
    }

    public static function getPayingState() {
        return self::$payingState;
    }

    public static function setPayingState($payingState) {
        self::$payingState = $payingState;
    }

    public static function getEmailAddress() {
        return self::$emailAddress;
    }

    public static function setEmailAddress($emailAddress) {
        self::$emailAddress = $emailAddress;
    }

    public static function getClient($mode = self::AUTHENTICATED) {
        if ($mode == self::AUTHENTICATED && !self::$key) {
            throw new AccountException("Provide an API key with Tinify\setKey(...)");
        }

        if (!self::$client) {
            self::$client = new Client(self::$key, self::$appIdentifier, self::$proxy);
        }

        return self::$client;
    }

    public static function setClient($client) {
        self::$client = $client;
    }
}

function setKey($key) {
    return Tinify::setKey($key);
}

function getKey() {
    return Tinify::getKey();
}

function createKey($email, $options) {
    return Tinify::createKey($email, $options);
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

function remainingCredits() {
    return Tinify::getRemainingCredits();
}

function payingState() {
    return Tinify::getPayingState();
}

function emailAddress() {
    return Tinify::getEmailAddress();
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
        Tinify::getClient()->request("get", "/keys/" . Tinify::getKey());
        return true;
    } catch (AccountException $err) {
        if ($err->status == 429) return true;
        throw $err;
    } catch (ClientException $err) {
        return true;
    }
}
