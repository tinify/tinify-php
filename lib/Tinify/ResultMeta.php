<?php

namespace Tinify;

class ResultMeta {
    protected $meta;

    /**
     * @param array $meta
     */
    public function __construct($meta) {
        $this->meta = $meta;
    }

    /**
     * @return int
     */
    public function width() {
        return intval($this->meta["image-width"]);
    }

    /**
     * @return int
     */
    public function height() {
        return intval($this->meta["image-height"]);
    }

    /**
     * @return string|null
     */
    public function location() {
        return isset($this->meta["location"]) ? $this->meta["location"] : null;
    }

    /**
     * @return string|null
     */
    public function extension() {
        if (isset($this->meta["content-type"])) {
            $parts = explode("/", $this->meta["content-type"]);
            return end($parts);
        }
        return null;
    }
}
