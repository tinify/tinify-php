<?php

namespace Tinify;

/**
 * Class containing compressed image meta data
 */
class ResultMeta {
    /**
     * The response headers containing image metadata.
     *
     * @var array $meta
     */
    protected $meta;

    /**
     * Constructs a ResultMeta with response metadata.
     *
     * @param array $meta The response headers containing image metadata.
     */
    public function __construct($meta) {
        $this->meta = $meta;
    }

    /**
     * Width of the image in pixels.
     * @return int The image width.
     */
    public function width() {
        return intval($this->meta["image-width"]);
    }

    /**
     * Height of the image in pixels.
     * @return int The image height.
     */
    public function height() {
        return intval($this->meta["image-height"]);
    }

    /**
     * Location of the compressed image.
     * @return string|null The URL to the compressed image, or null if not available.
     */
    public function location() {
        return isset($this->meta["location"]) ? $this->meta["location"] : null;
    }

    /**
     * Retrieves the file extension of the image.
     * Extracts the extension from the content-type header.
     * @return string|null The file extension or null if not available.
     */
    public function extension() {
        if (isset($this->meta["content-type"])) {
            $parts = explode("/", $this->meta["content-type"]);
            return end($parts);
        }
        return null;
    }
}
