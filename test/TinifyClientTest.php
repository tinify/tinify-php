<?php

use Tinify\CurlMock;

class TinifyClientTest extends TestCase {
    public function testRequestWhenValidShouldIssueRequest() {
        CurlMock::register("https://api.tinify.com/", array("status" => 200));
        $client = new Tinify\Client("key");
        $client->request("get", "/");

        $this->assertSame("https://api.tinify.com/", CurlMock::last(CURLOPT_URL));
        $this->assertSame("api:key", CurlMock::last(CURLOPT_USERPWD));
    }

    public function testRequestWhenValidShouldIssueRequestWithoutBodyWhenOptionsAreEmpty() {
        CurlMock::register("https://api.tinify.com/", array("status" => 200));
        $client = new Tinify\Client("key");
        $client->request("get", "/", array());

        $this->assertFalse(CurlMock::last_has(CURLOPT_POSTFIELDS));
    }

    public function testRequestWhenValidShouldIssueRequestWithoutContentTypeWhenOptionsAreEmpty() {
        CurlMock::register("https://api.tinify.com/", array("status" => 200));
        $client = new Tinify\Client("key");
        $client->request("get", "/", array());

        $this->assertFalse(CurlMock::last_has(CURLOPT_HTTPHEADER));
    }

    public function testRequestWhenValidShouldIssueRequestWithJSONBody() {
        CurlMock::register("https://api.tinify.com/", array("status" => 200));
        $client = new Tinify\Client("key");
        $client->request("get", "/", array("hello" => "world"));

        $this->assertSame(array("Content-Type: application/json"), CurlMock::last(CURLOPT_HTTPHEADER));
        $this->assertSame('{"hello":"world"}', CurlMock::last(CURLOPT_POSTFIELDS));
    }

    public function testRequestWhenValidShouldIssueRequestWithUserAgent() {
        CurlMock::register("https://api.tinify.com/", array("status" => 200));
        $client = new Tinify\Client("key");
        $client->request("get", "/");

        $this->assertSame(Tinify\Client::userAgent(), CurlMock::last(CURLOPT_USERAGENT));
    }

    public function testRequestWhenValidShouldUpdateCompressionCount() {
        CurlMock::register("https://api.tinify.com/", array(
            "status" => 200, "headers" => array("Compression-Count" => "12")
        ));
        $client = new Tinify\Client("key");
        $client->request("get", "/");

        $this->assertSame(12, Tinify\getCompressionCount());
    }

    public function testRequestWhenValidShouldUpdateRemainingCredits() {
        CurlMock::register("https://api.tinify.com/", array(
            "status" => 200, "headers" => array("Compression-Count-Remaining" => "488")
        ));
        $client = new Tinify\Client("key");
        $client->request("get", "/");

        $this->assertSame(488, Tinify\remainingCredits());
    }

    public function testRequestWhenValidShouldUpdatePayingState() {
        CurlMock::register("https://api.tinify.com/", array(
            "status" => 200, "headers" => array("Paying-State" => "free")
        ));
        $client = new Tinify\Client("key");
        $client->request("get", "/");

        $this->assertSame("free", Tinify\payingState());
    }

    public function testRequestWhenValidShouldUpdateEmailAddress() {
        CurlMock::register("https://api.tinify.com/", array(
            "status" => 200, "headers" => array("Email-Address" => "test@example.com")
        ));
        $client = new Tinify\Client("key");
        $client->request("get", "/");

        $this->assertSame("test@example.com", Tinify\emailAddress());
    }

    public function testRequestWhenValidWithAppIdShouldIssueRequestWithUserAgent() {
        CurlMock::register("https://api.tinify.com/", array("status" => 200));
        $client = new Tinify\Client("key", "TestApp/0.1");
        $client->request("get", "/");

        $this->assertSame(Tinify\Client::userAgent() . " TestApp/0.1", CurlMock::last(CURLOPT_USERAGENT));
    }

    public function testRequestWhenValidShouldParseJSONBody() {
        CurlMock::register("https://api.tinify.com/", array(
            "status" => 200,
            "body" => '{"hello":"world"}',
            "headers" => array("Content-Type" => "application/JSON; charset=utf-8")
        ));
        $client = new Tinify\Client("key");
        $response = $client->request("post", "/");
        $this->assertSame("world", $response->body->hello);
    }

    public function testRequestWhenValidShouldNotParseBinaryBody() {
        CurlMock::register("https://api.tinify.com/", array(
            "status" => 200,
            "body" => "binary body",
            "headers" => array("Content-Type" => "image/png")
        ));
        $client = new Tinify\Client("key");
        $response = $client->request("post", "/");

        $this->assertSame("binary body", $response->body);
    }

    public function testRequestWhenValidWithProxyShouldIssueRequestWithProxyAuthorization() {
        CurlMock::register("https://api.tinify.com/", array("status" => 200));
        $client = new Tinify\Client("key", NULL, "http://user:pass@localhost:8080");
        $client->request("get", "/");

        $this->assertSame("localhost", CurlMock::last(CURLOPT_PROXY));
        $this->assertSame(8080, CurlMock::last(CURLOPT_PROXYPORT));
        $this->assertSame("user:pass", CurlMock::last(CURLOPT_PROXYUSERPWD));
    }

    public function testRequestWithBadJSONBodyThrowExceptionWithMessage() {
        CurlMock::register("https://api.tinify.com/", array(
            "status" => 200,
            "body" => '<!-- this is not json -->',
            "headers" => array("Content-Type" => "application/JSON"),
        ));
        if (PHP_VERSION_ID >= 50500) {
            $this->setExpectedExceptionRegExp("Tinify\Exception",
                "/Error while parsing response: Syntax error \(#4\) \(HTTP 200\/ParseError\)/");
        } else {
            $this->setExpectedExceptionRegExp("Tinify\Exception",
                "/Error while parsing response: Error \(#4\) \(HTTP 200\/ParseError\)/");
        }
        $client = new Tinify\Client("key");
        $client->request("get", "/");
    }

    public function testRequestWithUnexpectedErrorOnceShouldReturnResponse() {
        CurlMock::register("https://api.tinify.com/", array(
            "error" => "Failed!", "errno" => 2
        ));

        CurlMock::register("https://api.tinify.com/", array("status" => 201));

        $client = new Tinify\Client("key");
        $response = $client->request("get", "/");
        $this->assertEquals("", $response->body);
    }

    public function testRequestWithUnexpectedErrorRepeatedlyShouldThrowConnectionException() {
        CurlMock::register("https://api.tinify.com/", array(
            "error" => "Failed!", "errno" => 2
        ));

        $this->setExpectedException("Tinify\ConnectionException");
        $client = new Tinify\Client("key");
        $client->request("get", "/");
    }

    public function testRequestWithUnexpectedErrorRepeatedlyShouldThrowExceptionWithMessage() {
        CurlMock::register("https://api.tinify.com/", array(
            "error" => "Failed!", "errno" => 2
        ));

        $this->setExpectedExceptionRegExp("Tinify\ConnectionException",
            "/Error while connecting: Failed! \(#2\)/");
        $client = new Tinify\Client("key");
        $client->request("get", "/");
    }

    public function testRequestWithCurlErrorOnceShouldReturnResponse() {
        CurlMock::register("https://api.tinify.com/", array(
            "errno" => 7, "error" => "Something failed", "return" => null
        ));

        CurlMock::register("https://api.tinify.com/", array("status" => 201));

        $client = new Tinify\Client("key");
        $response = $client->request("get", "/");
        $this->assertEquals("", $response->body);
    }

    public function testRequestWithCurlErrorRepeatedlyShouldThrowConnectionExeption() {
        CurlMock::register("https://api.tinify.com/", array(
            "errno" => 7, "error" => "Something failed", "return" => null
        ));

        $this->setExpectedException("Tinify\ConnectionException");
        $client = new Tinify\Client("key");
        $client->request("get", "/");
    }

    public function testRequestWithCurlErrorRepeatedlyShouldThrowExceptionWithMessage() {
        CurlMock::register("https://api.tinify.com/", array(
            "errno" => 7, "error" => "Something failed", "return" => null
        ));

        $this->setExpectedExceptionRegExp("Tinify\ConnectionException",
            "/Error while connecting/");
        $client = new Tinify\Client("key");
        $client->request("get", "/");
    }

    public function testRequestWithServerErrorOnceShouldReturnResponse() {
        CurlMock::register("https://api.tinify.com/", array(
            "status" => 584, "body" => '{"error":"InternalServerError","message":"Oops!"}'
        ));

        CurlMock::register("https://api.tinify.com/", array("status" => 201));

        $client = new Tinify\Client("key");
        $response = $client->request("get", "/");
        $this->assertEquals("", $response->body);
    }

    public function testRequestWithServerErrorRepeatedlyShouldThrowServerException() {
        CurlMock::register("https://api.tinify.com/", array(
            "status" => 584, "body" => '{"error":"InternalServerError","message":"Oops!"}'
        ));

        $this->setExpectedException("Tinify\ServerException");
        $client = new Tinify\Client("key");
        $client->request("get", "/");
    }

    public function testRequestWithBodyAndServerErrorRepeatedlyShouldThrowServerException() {
        CurlMock::register("https://api.tinify.com/", array(
            "body" => '{"email":"user@gmail.com"}'
        ), array(
            "status" => 500, "body" => '{"error":"InternalServerError","message":"Oops!"}'
        ));
        
        $this->setExpectedException("Tinify\ServerException");
        $client = new Tinify\Client("key");
        $response = $client->request("post", "/", array("email"=> "user@gmail.com"));
        $this->assertEquals("", $response->body);
    }

    public function testRequestWithServerErrorRepeatedlyShouldThrowExceptionWithMessage() {
        CurlMock::register("https://api.tinify.com/", array(
            "status" => 584, "body" => '{"error":"InternalServerError","message":"Oops!"}'
        ));

        $this->setExpectedExceptionRegExp("Tinify\ServerException",
            "/Oops! \(HTTP 584\/InternalServerError\)/");
        $client = new Tinify\Client("key");
        $client->request("get", "/");
    }

    public function testRequestWithBadServerResponseOnceShouldReturnResponse() {
        CurlMock::register("https://api.tinify.com/", array(
            "status" => 543, "body" => '<!-- this is not json -->'
        ));

        CurlMock::register("https://api.tinify.com/", array("status" => 201));

        $client = new Tinify\Client("key");
        $response = $client->request("get", "/");
        $this->assertEquals("", $response->body);
    }

    public function testRequestWithBadServerResponseRepeatedlyShouldThrowServerException() {
        CurlMock::register("https://api.tinify.com/", array(
            "status" => 543, "body" => '<!-- this is not json -->'
        ));

        $this->setExpectedException("Tinify\ServerException");
        $client = new Tinify\Client("key");
        $client->request("get", "/");
    }

    public function testRequestWithBadServerResponseRepeatedlyShouldThrowExceptionWithMessage() {
        CurlMock::register("https://api.tinify.com/", array(
            "status" => 543, "body" => '<!-- this is not json -->'
        ));

        if (PHP_VERSION_ID >= 50500) {
            $this->setExpectedExceptionRegExp("Tinify\ServerException",
                "/Error while parsing response: Syntax error \(#4\) \(HTTP 543\/ParseError\)/");
        } else {
            $this->setExpectedExceptionRegExp("Tinify\ServerException",
                "/Error while parsing response: Error \(#4\) \(HTTP 543\/ParseError\)/");
        }

        $client = new Tinify\Client("key");
        $client->request("get", "/");
    }

    public function testRequestWithClientErrorShouldThrowClientException() {
        CurlMock::register("https://api.tinify.com/", array(
            "status" => 492, "body" => '{"error":"BadRequest","message":"Oops!"}')
        );

        CurlMock::register("https://api.tinify.com/", array("status" => 201));

        $this->setExpectedException("Tinify\ClientException");
        $client = new Tinify\Client("key");
        $client->request("get", "/");
    }

    public function testRequestWithClientErrorShouldThrowExceptionWithMessage() {
        CurlMock::register("https://api.tinify.com/", array(
            "status" => 492, "body" => '{"error":"BadRequest","message":"Oops!"}'
        ));

        CurlMock::register("https://api.tinify.com/", array("status" => 201));

        $this->setExpectedExceptionRegExp("Tinify\ClientException",
            "/Oops! \(HTTP 492\/BadRequest\)/");
        $client = new Tinify\Client("key");
        $client->request("get", "/");
    }

    public function testRequestWithBadCredentialsShouldThrowAccountException() {
        CurlMock::register("https://api.tinify.com/", array(
            "status" => 401, "body" => '{"error":"Unauthorized","message":"Oops!"}'
        ));

        CurlMock::register("https://api.tinify.com/", array("status" => 201));

        $this->setExpectedException("Tinify\AccountException");
        $client = new Tinify\Client("key");
        $client->request("get", "/");
    }

    public function testRequestWithBadCredentialsShouldThrowExceptionWithMessage() {
        CurlMock::register("https://api.tinify.com/", array(
            "status" => 401, "body" => '{"error":"Unauthorized","message":"Oops!"}'
        ));

        CurlMock::register("https://api.tinify.com/", array("status" => 201));

        $this->setExpectedExceptionRegExp("Tinify\AccountException",
            "/Oops! \(HTTP 401\/Unauthorized\)/");
        $client = new Tinify\Client("key");
        $client->request("get", "/");
    }

    public function testRequestWithNoSSLCurlShouldThrowExceptionWithMessage() {
        CurlMock::register("https://api.tinify.com/", array("status" => 200));
        CurlMock::set_version_info_key("features", (CURL_VERSION_LIBZ | CURL_VERSION_IPV6));
        $this->setExpectedException("Tinify\ClientException",
            "Your curl version does not support secure connections");
        $client = new Tinify\Client("key");
        $client->request("get", "/");
    }

    public function testRequestWithOutdatedCurlShouldThrowExceptionWithMessage() {
        CurlMock::register("https://api.tinify.com/", array("status" => 200));
        CurlMock::set_version_info_key("version_number", 0x070f05);
        CurlMock::set_version_info_key("version", "7.15.5");
        $this->setExpectedException("Tinify\ClientException",
            "Your curl version 7.15.5 is outdated; please upgrade to 7.18.1 or higher");
        $client = new Tinify\Client("key");
        $client->request("get", "/");
    }
}
