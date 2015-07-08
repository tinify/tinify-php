<?php

namespace Tinify;

class Source {
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
        $commands = array_merge($this->commands, array("resize" => $options));
        return new self($this->url, $commands);
    }

    public function store($options) {
        $commands = array_merge($this->commands, array("store" => $options));
        $response = (new self($this->url, $commands))->execute();
        return new Result($response["headers"], $response["body"]);
    }

    public function result() {
        $response = $this->execute();
        return new Result($response["headers"], $response["body"]);
    }

    public function toFile($path) {
        return $this->result()->toFile($path);
    }

    public function toBuffer() {
        return $this->result()->toBuffer();
    }

    protected function execute() {
        return Tinify::getClient()->request("post", $this->url, $this->commands);
    }
}
