<?php

// The Completer

require_once("config.php");
require_once("lib.php");
use \Autocomplete\Config;
use \Autocomplete\Index;

$key = filter_input(INPUT_GET, 'key');
$context = filter_input(INPUT_GET, 'context');

$key || die('Give me something to complete from');
$index = Index::create(Config::$index['type'], Config::$index['params']);
$context && $index->setContext($context);

echo json_encode($index->byKey($key));
