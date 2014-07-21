<?php

include "../src/LukasRos/JSONInclude/JSONInclude.php";

$jsonInclude = new \LukasRos\JSONInclude\JSONInclude(array('silent' => false));

$parsedJson = $jsonInclude->parseFileWithIncludes('file1.txt');

print_r($parsedJson);