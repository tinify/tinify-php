<?php

use Tinify\CurlMock;

class TinifyFormatTest extends TestCase {
    public function testFormatShouldExposeSupportedMediaTypes() {
        $this->assertSame("image/webp", Tinify\Format::WEBP);
        $this->assertSame("image/png", Tinify\Format::PNG);
        $this->assertSame("image/jpeg", Tinify\Format::JPEG);
        $this->assertSame("image/jpg", Tinify\Format::JPG);
        $this->assertSame("image/avif", Tinify\Format::AVIF);
        $this->assertSame("image/jxl", Tinify\Format::JXL);
        $this->assertSame("*/*", Tinify\Format::ANY);
    }

    public function testConvertWithFormatShouldSerializeMediaType() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));

        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "converted file"
        ));

        Tinify\Source::fromBuffer("png file")->convert(array("type" => Tinify\Format::JXL))->toBuffer();
        $this->assertSame("{\"convert\":{\"type\":\"image\/jxl\"}}", CurlMock::last(CURLOPT_POSTFIELDS));
    }

    public function testConvertWithMultipleFormatsShouldSerializeMediaTypes() {
        Tinify\setKey("valid");

        CurlMock::register("https://api.tinify.com/shrink", array(
            "status" => 201, "headers" => array("Location" => "https://api.tinify.com/some/location")
        ));

        CurlMock::register("https://api.tinify.com/some/location", array(
            "status" => 200, "body" => "converted file"
        ));

        Tinify\Source::fromBuffer("png file")
            ->convert(array("type" => array(Tinify\Format::JXL, Tinify\Format::WEBP)))
            ->toBuffer();
        $this->assertSame(
            "{\"convert\":{\"type\":[\"image\/jxl\",\"image\/webp\"]}}",
            CurlMock::last(CURLOPT_POSTFIELDS)
        );
    }
}
