<?php

require_once("curl_mock.php");
require_once("vendor/autoload.php");

class TestCase extends \PHPUnit_Framework_TestCase {
    function tearDown() {
        Tinify\CurlMock::reset();
    }
}
