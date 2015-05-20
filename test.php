<?php

require_once('vendor/autoload.php');

Tinify\setKey("abc");
$r = Tinify\Tinify::getClient()->request("get","/");
