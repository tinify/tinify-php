<?php

class ClientTest extends TestCase {
    public function testResetShouldResetKey() {
        Tinify\setKey("abcde");
        Tinify\Tinify::reset();

        $prop = new ReflectionProperty("Tinify\Tinify", "key");
        $prop->setAccessible(true);
        $this->assertSame(NULL, $prop->getValue());
    }
}
