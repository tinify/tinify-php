<?php
abstract class TestCase_2 extends \PHPUnit\Framework\TestCase {
    protected  function setUp(): void {
        Tinify\CurlMock::reset();
        Tinify\setKey(NULL);
        TInify\setProxy(NULL);
    }

    protected function tearDown(): void {
        Tinify\CurlMock::reset();
    }

    protected function setExpectedException($class) {
        self::expectException($class);
    }

    protected function setExpectedExceptionRegExp($class, $matches) {
        self::setExpectedException($class);
        self::expectExceptionMessageMatches($matches);
    }
}
