<?php

namespace Tinify;

/**
 * Media types that images can be converted to.
 *
 * Use these constants with Source::convert():
 *
 *     \Tinify\fromFile("input.png")
 *         ->convert(array("type" => \Tinify\Format::JXL))
 *         ->toFile("output.jxl");
 *
 * Multiple types may be supplied, in which case the API returns the smallest
 * result:
 *
 *     array("type" => array(\Tinify\Format::JXL, \Tinify\Format::WEBP))
 */
final class Format {
    /** WebP. */
    const WEBP = "image/webp";

    /** PNG. */
    const PNG = "image/png";

    /** JPEG. */
    const JPEG = "image/jpeg";

    /**
     * JPEG, under the alternative `image/jpg` media type the API also
     * accepts. The API normalises this to `image/jpeg`, so the result is
     * identical to self::JPEG. The two constants hold different strings
     * but select the same format.
     */
    const JPG = "image/jpg";

    /** AVIF. */
    const AVIF = "image/avif";

    /** JPEG XL. */
    const JXL = "image/jxl";

    /** Wildcard, returns the smallest of the supported types. */
    const ANY = "*/*";

    private function __construct() {
    }
}
