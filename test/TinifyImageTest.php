<?php

use Tinify\CurlMock;

class TinifyImageTest extends TestCase {
    private $dummyFile;

    public function setUp() {
        $this->dummyFile = __DIR__ . "/examples/dummy.png";
    }

    public function testFromFileWithInvalidApiKeyShouldThrowAccountException() {
        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 401, "body" => '{"error":"Unauthorized","message":"Oops!"}'
        ));
        Tinify\setKey("invalid");

        $this->setExpectedException("Tinify\AccountException");
        Tinify\fromFile($this->dummyFile);
    }

    public function testFromBufferWithInvalidApiKeyShouldThrowAccountException() {
        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 401, "body" => '{"error":"Unauthorized","message":"Oops!"}'
        ));
        Tinify\setKey("invalid");

        $this->setExpectedException("Tinify\AccountException");
        Tinify\fromBuffer("png file");
    }

    public function testFromFileWithValidApiKeyShouldReturnImage() {
        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));
        Tinify\setKey("valid");
        $this->assertInstanceOf("Tinify\Image", Tinify\fromFile($this->dummyFile));
    }

    public function testFromFileWithValidApiKeyShouldReturnImageWithData() {
        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));
        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "compressed file"
        ));
        Tinify\setKey("valid");
        $this->assertSame("compressed file", Tinify\fromFile($this->dummyFile)->toBuffer());
    }

    public function testFromBufferWithValidApiKeyShouldReturnImage() {
        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));
        Tinify\setKey("valid");
        $this->assertInstanceOf("Tinify\Image", Tinify\fromBuffer("png file"));
    }

    public function testFromBufferWithValidApiKeyShouldReturnImageWithData() {
        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));
        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "compressed file"
        ));
        Tinify\setKey("valid");
        $this->assertSame("compressed file", Tinify\fromBuffer("png file")->toBuffer());
    }

    public function testResizeWithValidApiKeyShouldReturnImage() {
        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));
        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "small file"
        ));
        Tinify\setKey("valid");
        $this->assertInstanceOf("Tinify\Image", Tinify\fromBuffer("png file")->resize(array("width" => 400)));
    }

    public function testResizeWithValidApiKeyShouldReturnImageWithData() {
        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));
        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "small file"
        ));
        Tinify\setKey("valid");
        $this->assertSame("small file", Tinify\fromBuffer("png file")->resize(array("width" => 400))->toBuffer());
    }

    public function testToFileShouldStoreImageData() {
        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));
        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "compressed file"
        ));

        $path = tempnam(sys_get_temp_dir(), "tinify-php");
        Tinify\setKey("valid");
        Tinify\fromBuffer("png file")->toFile($path);
        $this->assertSame("compressed file", file_get_contents($path));
    }
}
