<?php

if (getenv("TRAVIS_PULL_REQUEST") && getenv("TRAVIS_PULL_REQUEST") != "false") {
    exit(0);
}

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

    public function testShouldCompress() {
        $path = tempnam(sys_get_temp_dir(), "tinify-php");
        self::$optimized->toFile($path);
        $this->assertGreaterThan(0, filesize($path));
        $this->assertLessThan(1500, filesize($path));
    }

    public function testShouldResize() {
        $path = tempnam(sys_get_temp_dir(), "tinify-php");
        self::$optimized->resize(array("method" => "fit", "width" => 50, "height" => 20))->toFile($path);
        $this->assertGreaterThan(0, filesize($path));
        $this->assertLessThan(800, filesize($path));
    }
}
