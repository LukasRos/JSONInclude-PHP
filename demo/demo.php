<?php

require_once "../vendor/autoload.php";

use LukasRos\JSONInclude\JSONInclude;

$jsonInclude = new JSONInclude(array('silent' => false));

$parsedJson = $jsonInclude->parseFileWithIncludes('file1.json');

print_r($parsedJson);