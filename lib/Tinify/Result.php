<?php

namespace Tinify;

/**
 * Represents a compressed image result from the Tinify API.
 * Contains the compressed image data and metadata such as content type and size.
 *
 * @see ResultMeta
 */
class Result extends ResultMeta {
    /**
     * The compressed image data.
     * 
     * @var string $data
     */
    protected $data;

    /**
     * Constructs a Result with metadata and image data.
     *
     * @param array $meta The response headers containing metadata.
     * @param string $data The compressed image data.
     * @return void
     */
    public function __construct($meta, $data) {
        $this->meta = $meta;
        $this->data = $data;
    }

    /**
     * Retrieves the compressed image data.
     *
     * @return string The compressed image data.
     */
    public function data() {
        return $this->data;
    }

    /**
     * Retrieves the compressed image as a buffer.
     *
     * Alias for data()
     * @see data()
     * 
     * @return string The compressed image data.
     */
    public function toBuffer() {
        return $this->data;
    }

    /**
     * Writes the compressed image to a file.
     *
     * @param string $path The file path where the image should be written.
     * @return int|false The bytes written, or false on failure.
     */
    public function toFile($path) {
        return file_put_contents($path, $this->toBuffer());
    }

    /**
     * Retrieves the size of the compressed image in bytes.
     * @return int The size of the compressed image.
     */
    public function size() {
        return intval($this->meta["content-length"]);
    }

    /**
     * Retrieves the media type of the compressed image.
     *
     * @return string The media type eg 'image/png' or 'image/jpeg'.
     */
    public function mediaType() {
        return $this->meta["content-type"];
    }

    /**
     * Retrieves the content type of the compressed image.
     *
     * Alias for mediaType()
     * @see mediaType()
     * 
     * @return string The content type e.g. 'image/png' or 'image/jpeg'.
     */
    public function contentType() {
        return $this->mediaType();
    }
}
