<?php

class TinifyExceptionTest extends TestCase {
    public function testStatusShouldReturnStatusIfSet() {
        $err = new Tinify\Exception("Message", "Error", 401);
        $this->assertSame(401, $err->status);
    }

    public function testStatusShouldReturnNullIfUnset() {
        $err = new Tinify\Exception("Message", "Error");
        $this->assertSame(null, $err->status);
    }
}
