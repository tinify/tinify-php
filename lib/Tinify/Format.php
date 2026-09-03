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

    /** JPEG, an alias of self::JPEG. */
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
