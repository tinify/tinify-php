<?php

require_once("vendor/autoload.php");
require_once("curl_mock.php");

class TestCase extends \PHPUnit_Framework_TestCase {
    function setUp() {
        \Tinify\CurlMock::reset();
        \Tinify\setKey(NULL);
        \TInify\setProxy(NULL);
    }

    function tearDown() {
        \Tinify\CurlMock::reset();
    }
}
