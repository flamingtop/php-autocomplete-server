<?php

/**
 * @file
 * The Completer
 *
 */

require_once("config.php");
require_once("lib.php");
use \Autocomplete\Config;
use \Autocomplete\Index;

$key = $context = NULL;

if (php_sapi_name() == 'cli') {
    $opt = getopt('', array('key:', 'context::'));
    isset($opt['key']) && $key = $opt['key'];
    isset($opt['context']) && $context = $opt['context'];
} else {
    $key = filter_input(INPUT_GET, 'key');
    $context = filter_input(INPUT_GET, 'context');
}

$key || die('invalid key');

$index = Index::create(Config::$index['type'], Config::$index['params']);
$context && $index->setContext($context);

echo json_encode($index->byKey($key));
