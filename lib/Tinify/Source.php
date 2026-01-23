<?php

namespace Tinify;

/**
 * Represents an compressed image source.
 */
class Source {
    private $url, $commands;

    /**
     * Shrinks the file at the given path
     * - reads the file from disk
     * - uses fromBuffer to make the API request
     *
     * @param string $path The path to the file
     * @return Source
     */
    public static function fromFile($path) {
        return self::fromBuffer(file_get_contents($path));
    }

    /**
     * Shrinks the given string.
     * Will upload and compress the image to Tinify
     * 
     *
     * @param string $string the raw body
     * @return Source
     */
    public static function fromBuffer($string) {
        $response = Tinify::getClient()->request("post", "/shrink", $string);
        return new self($response->headers["location"]);
    }

    /**
     * Shrinks the image on the given url
     *
     * @param string $url url to the subject to shrink
     * @return Source
     */
    public static function fromUrl($url) {
        $body = array("source" => array("url" => $url));
        $response = Tinify::getClient()->request("post", "/shrink", $body);
        return new self($response->headers["location"]);
    }

    /**
     * Constructor for the image source that has been compressed.
     *
     * @param string $url url to the compressed image
     * @param array $commands Array of actions to apply on the compressed image.
     * @return void
     */
    public function __construct($url, $commands = array()) {
        $this->url = $url;
        $this->commands = $commands;
    }

    /**
     * Will add preserve command. This will keep meta data on the compressed image.
     * https://tinypng.com/developers/reference#preserving-metadata
     *
     * @return Source
     */
    public function preserve() {
        $options = $this->flatten(func_get_args());
        $commands = array_merge($this->commands, array("preserve" => $options));
        return new self($this->url, $commands);
    }

    /**
     * Will add resize command to the compressed image.
     * https://tinypng.com/developers/reference#resizing-images
     *
     * @param array $options resize options
     * @return Source
     */
    public function resize($options) {
        $commands = array_merge($this->commands, array("resize" => $options));
        return new self($this->url, $commands);
    }

    /**
     * Will save the image to cloud storage
     * https://tinypng.com/developers/reference#saving-to-amazon-s3
     * Supports GCP, S3 and any other S3 API compatible storage.
     *
     * @param array $options store options
     * @return Result The stored image
     */
    public function store($options) {
        $response = Tinify::getClient()->request("post", $this->url,
            array_merge($this->commands, array("store" => $options)));
        return new Result($response->headers, $response->body);
    }

    /**
     * Will add convert command to the image
     * https://tinypng.com/developers/reference#converting-images
     *
     * @param array $options conversion options
     * @return Source
     */
    public function convert($options) {
        $commands = array_merge($this->commands, array("convert" => $options));
        return new self($this->url, $commands);
    }

    /**
     * Will add transform commands to the image
     * https://tinypng.com/developers/reference#converting-images
     * The transform object specifies the stylistic transformations that will be applied to your image.
     *
     * @param array $options
     * @return Source
     */
    public function transform($options) {
        $commands = array_merge($this->commands, array("transform" => $options));
        return new self($this->url, $commands);
    }

    /**
     * Retrieves the compressed image as a Result object.
     *
     * Sends a GET request if no commands have been applied,
     * or a POST request if commands are present.
     *
     * @return Result The compressed image with metadata.
     */
    public function result() {
        $has_commands  = !empty($this->commands);
        $method = $has_commands ? "post" : "get";
        $body = $has_commands ? $this->commands : null;
        $response = Tinify::getClient()->request($method, $this->url, $body);
        return new Result($response->headers, $response->body);
    }

    /**
     * Retrieves the compressed image and stores it on the given path
     * 
     *
     * @param string $path Local file path
     * @return int|false Returns positive int if succesful
     */
    public function toFile($path) {
        return $this->result()->toFile($path);
    }

    /**
     * Retrieves the raw image data
     *
     * @return string Raw result body
     */
    public function toBuffer() {
        return $this->result()->toBuffer();
    }

    /**
     * Flattens an array of options.
     *
     * @param array $options An array that may contain mixed values and nested arrays.
     * @return array A flattened array with all nested arrays merged.
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
