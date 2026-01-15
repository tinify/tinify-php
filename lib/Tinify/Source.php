<?php

namespace Tinify;

class Source {
    private $url, $commands;

    /**
     * @param string $path
     * @return Source
     * @throws AccountException
     * @throws ClientException
     * @throws ServerException
     * @throws ConnectionException
     */
    public static function fromFile($path) {
        return self::fromBuffer(file_get_contents($path));
    }

    /**
     * @param string $string
     * @return Source
     * @throws AccountException
     * @throws ClientException
     * @throws ServerException
     * @throws ConnectionException
     */
    public static function fromBuffer($string) {
        $response = Tinify::getClient()->request("post", "/shrink", $string);
        return new self($response->headers["location"]);
    }

    /**
     * @param string $url
     * @return Source
     * @throws AccountException
     * @throws ClientException
     * @throws ServerException
     * @throws ConnectionException
     */
    public static function fromUrl($url) {
        $body = array("source" => array("url" => $url));
        $response = Tinify::getClient()->request("post", "/shrink", $body);
        return new self($response->headers["location"]);
    }

    /**
     * @param string $url
     * @param array $commands
     */
    public function __construct($url, $commands = array()) {
        $this->url = $url;
        $this->commands = $commands;
    }

    /**
     * @return Source
     */
    public function preserve() {
        $options = $this->flatten(func_get_args());
        $commands = array_merge($this->commands, array("preserve" => $options));
        return new self($this->url, $commands);
    }

    /**
     * @param array $options
     * @return Source
     */
    public function resize($options) {
        $commands = array_merge($this->commands, array("resize" => $options));
        return new self($this->url, $commands);
    }

    /**
     * @param array $options
     * @return Result
     * @throws AccountException
     * @throws ClientException
     * @throws ServerException
     * @throws ConnectionException
     */
    public function store($options) {
        $response = Tinify::getClient()->request("post", $this->url,
            array_merge($this->commands, array("store" => $options)));
        return new Result($response->headers, $response->body);
    }

    /**
     * @param array $options
     * @return Source
     */
    public function convert($options) {
        $commands = array_merge($this->commands, array("convert" => $options));
        return new self($this->url, $commands);
    }

    /**
     * @param array $options
     * @return Source
     */
    public function transform($options) {
        $commands = array_merge($this->commands, array("transform" => $options));
        return new self($this->url, $commands);
    }

    /**
     * @return Result
     * @throws AccountException
     * @throws ClientException
     * @throws ServerException
     * @throws ConnectionException
     */
    public function result() {
        $has_commands  = !empty($this->commands);
        $method = $has_commands ? "post" : "get";
        $body = $has_commands ? $this->commands : null;
        $response = Tinify::getClient()->request($method, $this->url, $body);
        return new Result($response->headers, $response->body);
    }

    /**
     * @param string $path
     * @return int|false
     * @throws AccountException
     * @throws ClientException
     * @throws ServerException
     * @throws ConnectionException
     */
    public function toFile($path) {
        return $this->result()->toFile($path);
    }

    /**
     * @return string
     * @throws AccountException
     * @throws ClientException
     * @throws ServerException
     * @throws ConnectionException
     */
    public function toBuffer() {
        return $this->result()->toBuffer();
    }

    /**
     * @param array $options Array of options from func_get_args()
     * @return array Flattened array
     */
    private static function flatten($options) {
        $flattened = array();
        foreach ($options as $option) {
            if (is_array($option)) {
                $flattened = array_merge($flattened, $option);
            } else {
                array_push($flattened, $option);
            }
        }
        return $flattened;
    }
}
