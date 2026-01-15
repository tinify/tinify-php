<?php

namespace Tinify;

class Result extends ResultMeta {
    protected $data;

    /**
     * @param array $meta
     * @param string $data
     */
    public function __construct($meta, $data) {
        $this->meta = $meta;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function data() {
        return $this->data;
    }

    /**
     * @return string
     */
    public function toBuffer() {
        return $this->data;
    }

    /**
     * @param string $path
     * @return int|false
     */
    public function toFile($path) {
        return file_put_contents($path, $this->toBuffer());
    }

    /**
     * @return int
     */
    public function size() {
        return intval($this->meta["content-length"]);
    }

    /**
     * @return string
     */
    public function mediaType() {
        return $this->meta["content-type"];
    }

    /**
     * @return string
     */
    public function contentType() {
        return $this->mediaType();
    }
}
