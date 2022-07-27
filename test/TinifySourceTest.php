<?php

use Tinify\CurlMock;

class TinifySourceTest extends TestCase {
    public function testWithInvalidApiKeyFromFileShouldThrowAccountException() {
        Tinify\setKey("invalid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 401, "body" => '{"error":"Unauthorized","message":"Credentials are invalid"}'
        ));

        $this->setExpectedException("Tinify\AccountException");
        Tinify\Source::fromFile(DUMMY_FILE_LOCATION);
    }

    public function testWithInvalidApiKeyFromBufferShouldThrowAccountException() {
        Tinify\setKey("invalid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 401, "body" => '{"error":"Unauthorized","message":"Credentials are invalid"}'
        ));

        $this->setExpectedException("Tinify\AccountException");
        Tinify\Source::fromBuffer("png file");
    }

    public function testWithInvalidApiKeyFromUrlShouldThrowAccountException() {
        Tinify\setKey("invalid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 401, "body" => '{"error":"Unauthorized","message":"Credentials are invalid"}'
        ));

        $this->setExpectedException("Tinify\AccountException");
        Tinify\Source::fromUrl("http://example.com/test.jpg");
    }

    public function testWithValidApiKeyFromFileShouldReturnSource() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));

        $this->assertInstanceOf("Tinify\Source", Tinify\Source::fromFile(DUMMY_FILE_LOCATION));
    }

    public function testWithValidApiKeyFromFileShouldReturnSourceWithData() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));

        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "compressed file"
        ));

        $this->assertSame("compressed file", Tinify\Source::fromFile(DUMMY_FILE_LOCATION)->toBuffer());
    }

    public function testWithValidApiKeyFromBufferShouldReturnSource() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));

        $this->assertInstanceOf("Tinify\Source", Tinify\Source::fromBuffer("png file"));
    }

    public function testWithValidApiKeyFromBufferShouldReturnSourceWithData() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));

        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "compressed file"
        ));

        $this->assertSame("compressed file", Tinify\Source::fromBuffer("png file")->toBuffer());
    }

    public function testWithValidApiKeyFromUrlShouldReturnSource() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));

        $this->assertInstanceOf("Tinify\Source", Tinify\Source::fromUrl("http://example.com/testWithValidApiKey.jpg"));
    }

    public function testWithValidApiKeyFromUrlShouldReturnSourceWithData() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));

        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "compressed file"
        ));

        $this->assertSame("compressed file", Tinify\Source::fromUrl("http://example.com/testWithValidApiKey.jpg")->toBuffer());
    }

    public function testWithValidApiKeyFromUrlShouldThrowExceptionIfRequestIsNotOK() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 400, "body" => '{"error":"Source not found","message":"Cannot parse URL"}'
        ));

        $this->setExpectedException("Tinify\ClientException");
        Tinify\Source::fromUrl("file://wrong");
    }

    public function testWithValidApiKeyResultShouldReturnResult() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201,
            "headers" => array("Location" => "https://api.tinify.com/some/location"),
        ));

        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "compressed file"
        ));

        $this->assertInstanceOf("Tinify\Result", Tinify\Source::fromBuffer("png file")->result());
    }

    public function testWithValidApiKeyPreserveShouldReturnSource() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));

        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "copyrighted file"
        ));

        $this->assertInstanceOf("Tinify\Source", Tinify\Source::fromBuffer("png file")->preserve("copyright", "location"));
        $this->assertSame("png file", CurlMock::last(CURLOPT_POSTFIELDS));
    }

    public function testWithValidApiKeyPreserveShouldReturnSourceWithData() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));

        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "copyrighted file"
        ));

        $this->assertSame("copyrighted file", Tinify\Source::fromBuffer("png file")->preserve("copyright", "location")->toBuffer());
        $this->assertSame("{\"preserve\":[\"copyright\",\"location\"]}", CurlMock::last(CURLOPT_POSTFIELDS));
    }

    public function testWithValidApiKeyPreserveShouldReturnSourceWithDataForArray() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));

        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "copyrighted file"
        ));

        $this->assertSame("copyrighted file", Tinify\Source::fromBuffer("png file")->preserve(array("copyright", "location"))->toBuffer());
        $this->assertSame("{\"preserve\":[\"copyright\",\"location\"]}", CurlMock::last(CURLOPT_POSTFIELDS));
    }

    public function testWithValidApiKeyPreserveShouldIncludeOtherOptionsIfSet() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));

        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "copyrighted resized file"
        ));

        $source = Tinify\Source::fromBuffer("png file")->resize(array("width" => 400))->preserve(array("copyright", "location"));

        $this->assertSame("copyrighted resized file", $source->toBuffer());
        $this->assertSame("{\"resize\":{\"width\":400},\"preserve\":[\"copyright\",\"location\"]}", CurlMock::last(CURLOPT_POSTFIELDS));
    }

    public function testWithValidApiKeyResizeShouldReturnSource() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));

        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "small file"
        ));

        $this->assertInstanceOf("Tinify\Source", Tinify\Source::fromBuffer("png file")->resize(array("width" => 400)));
        $this->assertSame("png file", CurlMock::last(CURLOPT_POSTFIELDS));
    }

    public function testWithValidApiKeyResizeShouldReturnSourceWithData() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));

        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "small file"
        ));

        $this->assertSame("small file", Tinify\Source::fromBuffer("png file")->resize(array("width" => 400))->toBuffer());
        $this->assertSame("{\"resize\":{\"width\":400}}", CurlMock::last(CURLOPT_POSTFIELDS));
    }

    public function testTranscodeShouldReturnSource() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));

        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "transcoded file"
        ));

        $this->assertInstanceOf("Tinify\Source", Tinify\Source::fromBuffer("png file")->transcode("image/webp"));
        $this->assertSame("png file", CurlMock::last(CURLOPT_POSTFIELDS));
    }

    public function testTranscodeShouldReturnSourceWithData() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));

        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "transcoded file"
        ));

        $this->assertSame("transcoded file", Tinify\Source::fromBuffer("png file")->transcode("image/webp")->toBuffer());
        $this->assertSame("{\"type\":\"image\/webp\"}", CurlMock::last(CURLOPT_POSTFIELDS));
    }

    public function testTransformShouldReturnSource() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));

        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "transformed file"
        ));

        $this->assertInstanceOf("Tinify\Source", Tinify\Source::fromBuffer("png file")->transform(array("background" => "black")));
        $this->assertSame("png file", CurlMock::last(CURLOPT_POSTFIELDS));
    }

    public function testTransformShouldReturnSourceWithData() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));

        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "transformd file"
        ));

        $this->assertSame("transformd file", Tinify\Source::fromBuffer("png file")->transform(array("background" => "black"))->toBuffer());
        $this->assertSame("{\"transform\":{\"background\":\"black\"}}", CurlMock::last(CURLOPT_POSTFIELDS));
    }

    public function testWithValidApiKeyStoreShouldReturnResultMeta() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201,
            "headers" => array("Location" => "https://api.tinify.com/some/location"),
        ));

        CurlMock::register("https://api.tinify.com/some/location", array(
            "body" => '{"store":{"service":"s3","aws_secret_access_key":"abcde"}}'
        ), array("status" => 200));

        $options = array("service" => "s3", "aws_secret_access_key" => "abcde");
        $this->assertInstanceOf("Tinify\Result", Tinify\Source::fromBuffer("png file")->store($options));
        $this->assertSame("{\"store\":{\"service\":\"s3\",\"aws_secret_access_key\":\"abcde\"}}", CurlMock::last(CURLOPT_POSTFIELDS));
    }

    public function testWithValidApiKeyStoreShouldReturnResultMetaWithLocation() {
        Tinify\setKey("valid");

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

        $location = Tinify\Source::fromBuffer("png file")->store(array("service" => "s3"))->location();
        $this->assertSame("https://bucket.s3.amazonaws.com/example", $location);
        $this->assertSame("{\"store\":{\"service\":\"s3\"}}", CurlMock::last(CURLOPT_POSTFIELDS));
    }

    public function testWithValidApiKeyStoreShouldIncludeOtherOptionsIfSet() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201,
            "headers" => array("Location" => "https://api.tinify.com/some/location"),
        ));

        CurlMock::register("https://api.tinify.com/some/location", array(
            "body" => '{"resize":{"width":300},"store":{"service":"s3","aws_secret_access_key":"abcde"}}'
        ), array("status" => 200));

        $options = array("service" => "s3", "aws_secret_access_key" => "abcde");
        $this->assertInstanceOf("Tinify\Result", Tinify\Source::fromBuffer("png file")->resize(array("width" => 300))->store($options));
        $this->assertSame("{\"resize\":{\"width\":300},\"store\":{\"service\":\"s3\",\"aws_secret_access_key\":\"abcde\"}}", CurlMock::last(CURLOPT_POSTFIELDS));
    }

    public function testWithValidApiKeyToBufferShouldReturnImageData() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));
        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "compressed file"
        ));

        $this->assertSame("compressed file", Tinify\Source::fromBuffer("png file")->toBuffer());
    }

    public function testWithValidApiKeyToFileShouldStoreImageData() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));

        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "compressed file"
        ));

        $path = tempnam(sys_get_temp_dir(), "tinify-php");
        Tinify\Source::fromBuffer("png file")->toFile($path);
        $this->assertSame("compressed file", file_get_contents($path));
    }
}
