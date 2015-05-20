<?php

namespace Tinify;

const VERSION = "0.9.0";

class Tinify {
    private static $key = NULL;
    private static $appIdentifier = NULL;
    private static $client = NULL;

    public static function setKey($value) {
        self::$key = $value;
    }

    public static function setAppIdentifier($value) {
        self::$appIdentifier = $value;
    }

    public static function reset() {
        self::$key = NULL;
        self::$client = NULL;
    }

    public static function getClient() {
        if (!self::$client) {
            self::$client = new Client(self::$key);
        }
        return self::$client;
    }
}

function setKey($key) {
    Tinify::setKey($key);
}

function setAppIdentifier($key) {
    Tinify::setAppIdentifier($key);
}

function fromFile($path) {
    return Image::fromFile($path);
}

function fromBuffer($string) {
    return Image::fromBuffer($string);
}
