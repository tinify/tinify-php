<?php

use Tinify\CurlMock;

class TinifySourceTest extends TestCase {
    private $dummyFile;

    public function setUp() {
        parent::setUp();
        $this->dummyFile = __DIR__ . "/examples/dummy.png";
    }

    public function testWithInvalidApiKeyFromFileShouldThrowAccountException() {
        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 401, "body" => '{"error":"Unauthorized","message":"Credentials are invalid"}'
        ));
        Tinify\setKey("invalid");

        $this->setExpectedException("Tinify\AccountException");
        Tinify\Source::fromFile($this->dummyFile);
    }

    public function testWithInvalidApiKeyFromBufferShouldThrowAccountException() {
        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 401, "body" => '{"error":"Unauthorized","message":"Credentials are invalid"}'
        ));
        Tinify\setKey("invalid");

        $this->setExpectedException("Tinify\AccountException");
        Tinify\Source::fromBuffer("png file");
    }

    public function testWithInvalidApiKeyFromUrlShouldThrowAccountException() {
        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 401, "body" => '{"error":"Unauthorized","message":"Credentials are invalid"}'
        ));
        Tinify\setKey("invalid");

        $this->setExpectedException("Tinify\AccountException");
        Tinify\Source::fromUrl("http://example.com/test.jpg");
    }

    public function testWithValidApiKeyFromFileShouldReturnSource() {
        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));
        Tinify\setKey("valid");
        $this->assertInstanceOf("Tinify\Source", Tinify\Source::fromFile($this->dummyFile));
    }

    public function testWithValidApiKeyFromFileShouldReturnSourceWithData() {
        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));
        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "compressed file"
        ));
        Tinify\setKey("valid");
        $this->assertSame("compressed file", Tinify\Source::fromFile($this->dummyFile)->toBuffer());
    }

    public function testWithValidApiKeyFromBufferShouldReturnSource() {
        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));
        Tinify\setKey("valid");
        $this->assertInstanceOf("Tinify\Source", Tinify\Source::fromBuffer("png file"));
    }

    public function testWithValidApiKeyFromBufferShouldReturnSourceWithData() {
        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));
        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "compressed file"
        ));
        Tinify\setKey("valid");
        $this->assertSame("compressed file", Tinify\Source::fromBuffer("png file")->toBuffer());
    }

    public function testWithValidApiKeyFromUrlShouldReturnSource() {
        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));
        Tinify\setKey("valid");
        $this->assertInstanceOf("Tinify\Source", Tinify\Source::fromUrl("http://example.com/testWithValidApiKey.jpg"));
    }

    public function testWithValidApiKeyFromUrlShouldReturnSourceWithData() {
        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));
        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "compressed file"
        ));
        Tinify\setKey("valid");
        $this->assertSame("compressed file", Tinify\Source::fromUrl("http://example.com/testWithValidApiKey.jpg")->toBuffer());
    }

    public function testWithValidApiKeyFromUrlShouldThrowExceptionIfRequestIsNotOK() {
        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 400, "body" => '{"error":"Source not found","message":"Cannot parse URL"}'
        ));
        Tinify\setKey("valid");

        $this->setExpectedException("Tinify\ClientException");
        Tinify\Source::fromUrl("file://wrong");
    }

    public function testWithValidApiKeyResultShouldReturnResult() {
        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201,
            "headers" => array("Location" => "https://api.tinify.com/some/location"),
        ));

        Tinify\setKey("valid");
        $this->assertInstanceOf("Tinify\Result", Tinify\Source::fromBuffer("png file")->result());
    }

    public function testWithValidApiKeyResizeShouldReturnSource() {
        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));
        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "small file"
        ));
        Tinify\setKey("valid");
        $this->assertInstanceOf("Tinify\Source", Tinify\Source::fromBuffer("png file")->resize(array("width" => 400)));
    }

    public function testWithValidApiKeyResizeShouldReturnSourceWithData() {
        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));
        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "small file"
        ));
        Tinify\setKey("valid");
        $this->assertSame("small file", Tinify\Source::fromBuffer("png file")->resize(array("width" => 400))->toBuffer());
    }

    public function testWithValidApiKeyStoreShouldReturnResultMeta() {
        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201,
            "headers" => array("Location" => "https://api.tinify.com/some/location"),
        ));

        CurlMock::register("https://api.tinify.com/some/location", array(
            "body" => '{"store":{"service":"s3","aws_secret_access_key":"abcde"}}'
        ), array("status" => 200));

        Tinify\setKey("valid");
        $options = array("service" => "s3", "aws_secret_access_key" => "abcde");
        $this->assertInstanceOf("Tinify\Result", Tinify\Source::fromBuffer("png file")->store($options));
    }

    public function testWithValidApiKeyStoreShouldReturnResultMetaWithLocation() {
        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201,
            "headers" => array("Location" => "https://api.tinify.com/some/location"),
        ));

        CurlMock::register("https://api.tinify.com/some/location", array(
            "body" => '{"store":{"service":"s3"}}'
        ), array(
            "status" => 201,
            "headers" => array("Location" => "https://bucket.s3.amazonaws.com/example"),
        ));

        Tinify\setKey("valid");
        $location = Tinify\Source::fromBuffer("png file")->store(array("service" => "s3"))->location();
        $this->assertSame("https://bucket.s3.amazonaws.com/example", $location);
    }

    public function testWithValidApiKeyStoreShouldMergeCommands() {
        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201,
            "headers" => array("Location" => "https://api.tinify.com/some/location"),
        ));

        CurlMock::register("https://api.tinify.com/some/location", array(
            "body" => '{"resize":{"width":300},"store":{"service":"s3","aws_secret_access_key":"abcde"}}'
        ), array("status" => 200));

        Tinify\setKey("valid");
        $options = array("service" => "s3", "aws_secret_access_key" => "abcde");
        $this->assertInstanceOf("Tinify\Result", Tinify\Source::fromBuffer("png file")->resize(array("width" => 300))->store($options));
    }

    public function testWithValidApiKeyToBufferShouldReturnImageData() {
        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));
        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "compressed file"
        ));

        Tinify\setKey("valid");
        $this->assertSame("compressed file", Tinify\Source::fromBuffer("png file")->toBuffer());
    }

    public function testWithValidApiKeyToFileShouldStoreImageData() {
        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));
        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "compressed file"
        ));

        $path = tempnam(sys_get_temp_dir(), "tinify-php");
        Tinify\setKey("valid");
        Tinify\Source::fromBuffer("png file")->toFile($path);
        $this->assertSame("compressed file", file_get_contents($path));
    }
}
