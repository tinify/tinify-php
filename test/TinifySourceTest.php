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

    public function testFromFileShouldReturnSource() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));

        $this->assertInstanceOf("Tinify\Source", Tinify\Source::fromFile(DUMMY_FILE_LOCATION));
    }

    public function testFromFileShouldReturnSourceWithData() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));

        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "compressed file"
        ));

        $this->assertSame("compressed file", Tinify\Source::fromFile(DUMMY_FILE_LOCATION)->toBuffer());
    }

    public function testFromBufferShouldReturnSource() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));

        $this->assertInstanceOf("Tinify\Source", Tinify\Source::fromBuffer("png file"));
    }

    public function testFromBufferShouldReturnSourceWithData() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));

        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "compressed file"
        ));

        $this->assertSame("compressed file", Tinify\Source::fromBuffer("png file")->toBuffer());
    }

    public function testFromUrlShouldReturnSource() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));

        $this->assertInstanceOf("Tinify\Source", Tinify\Source::fromUrl("http://example.com/test.jpg"));
    }

    public function testFromUrlShouldReturnSourceWithData() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));

        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "compressed file"
        ));

        $this->assertSame("compressed file", Tinify\Source::fromUrl("http://example.com/test.jpg")->toBuffer());
    }

    public function testFromUrlShouldThrowExceptionIfRequestIsNotOK() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 400, "body" => '{"error":"Source not found","message":"Cannot parse URL"}'
        ));

        $this->setExpectedException("Tinify\ClientException");
        Tinify\Source::fromUrl("file://wrong");
    }

    public function testResultShouldReturnResult() {
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

    public function testPreserveShouldReturnSource() {
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

    public function testPreserveShouldReturnSourceWithData() {
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

    public function testPreserveShouldReturnSourceWithDataForArray() {
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

    public function testPreserveShouldIncludeOtherOptionsIfSet() {
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

    public function testResizeShouldReturnSource() {
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

    public function testResizeShouldReturnSourceWithData() {
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

    public function testConvertShouldReturnSource() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));

        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "Convertd file"
        ));

        $this->assertInstanceOf("Tinify\Source", Tinify\Source::fromBuffer("png file")->Convert(array("type" =>"image/webp")));
        $this->assertSame("png file", CurlMock::last(CURLOPT_POSTFIELDS));
    }

    public function testConvertShouldReturnSourceWithData() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));

        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "Convertd file"
        ));

        $this->assertSame("Convertd file", Tinify\Source::fromBuffer("png file")->convert(array("type" => "image/webp"))->toBuffer());
        $this->assertSame("{\"convert\":{\"type\":\"image\/webp\"}}", CurlMock::last(CURLOPT_POSTFIELDS));
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

    public function testStoreShouldReturnResultMeta() {
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

    public function testStoreShouldReturnResultMetaWithLocation() {
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

    public function testStoreShouldIncludeOtherOptionsIfSet() {
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

    public function testToBufferShouldReturnImageData() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));
        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "compressed file"
        ));

        $this->assertSame("compressed file", Tinify\Source::fromBuffer("png file")->toBuffer());
    }

    public function testToFileShouldStoreImageData() {
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
