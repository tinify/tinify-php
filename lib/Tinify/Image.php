<?php

namespace Tinify;

class Image {
    private $url, $commands;

    public static function fromFile($path) {
        return self::fromBuffer(file_get_contents($path));
    }

    public static function fromBuffer($string) {
        $response = Tinify::getClient()->request("post", "/shrink", $string);
        return new self($response["headers"]["location"]);
    }

    public function __construct($url, $commands = array()) {
        $this->url = $url;
        $this->commands = $commands;
    }

    public function resize($options) {
        return new self($this->url, array_merge($this->commands, array("resize" => $options)));
    }

    public function toFile($path) {
        return file_put_contents($path, $this->toBuffer());
    }

    public function toBuffer() {
        $response = Tinify::getClient()->request("post", $this->url, $this->commands);
        return $response["body"];
    }
}
