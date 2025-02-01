<?php
abstract class TestCase_1 extends \PHPUnit\Framework\TestCase {
    protected  function setUp() {
        Tinify\CurlMock::reset();
        Tinify\setKey(NULL);
        TInify\setProxy(NULL);
    }

    protected function tearDown() {
        Tinify\CurlMock::reset();
    }
}
