<?php

require_once("vendor" . DIRECTORY_SEPARATOR . "autoload.php");
require_once("curl_mock.php");


# Some PHPUnit glue to support testing PHP 5.4 and onwards together

if (!class_exists('PHPUnit\Runner\Version')) {
    class_alias('PHPUnit_Runner_Version', 'PHPUnit\Runner\Version');
}

if (version_compare(PHPUnit\Runner\Version::id(), '8') >= 0) {
    require_once("base" . DIRECTORY_SEPARATOR . "test_case_2.php");
    class_alias('TestCase_2', 'TestCase');
} else {
    require_once("base" . DIRECTORY_SEPARATOR . "test_case_1.php");
    class_alias('TestCase_1', 'TestCase');
}

define('DUMMY_FILE_LOCATION', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'test' . DIRECTORY_SEPARATOR . 'examples' . DIRECTORY_SEPARATOR . 'dummy.png');
