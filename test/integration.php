<?php

if (!getenv("TINIFY_KEY")) {
    exit("Set the TINIFY_KEY environment variable.\n");
}

class ClientIntegrationTest extends PHPUnit_Framework_TestCase {
    static private $optimized;

    static public function setUpBeforeClass() {
        \Tinify\setKey(getenv("TINIFY_KEY"));

        $unoptimizedPath = __DIR__ . "/examples/voormedia.png";
        self::$optimized = \Tinify\fromFile($unoptimizedPath);
    }

    public function testShouldCompressFromFile() {
        $path = tempnam(sys_get_temp_dir(), "tinify-php");
        self::$optimized->toFile($path);
        $this->assertGreaterThan(0, filesize($path));
        $this->assertLessThan(1500, filesize($path));
    }

    public function testShouldResize() {
        $path = tempnam(sys_get_temp_dir(), "tinify-php");
        self::$optimized->resize(array("method" => "fit", "width" => 50, "height" => 20))->toFile($path);
        $this->assertGreaterThan(0, filesize($path));
        $this->assertLessThan(1000, filesize($path));
    }

    public function testShouldCompressFromUrl() {
        $path = tempnam(sys_get_temp_dir(), "tinify-php");
        $optimized = \Tinify\fromUrl("https://raw.githubusercontent.com/tinify/tinify-php/master/test/examples/voormedia.png");
        $optimized->toFile($path);
        $this->assertGreaterThan(0, filesize($path));
        $this->assertLessThan(1500, filesize($path));
    }
}
