<?php

/**
 * @file
 * The Completer
 *
 * Use via HTTP
 *   /?key=h
 *   /?key=h&context=..
 *
 * Use via CLI
 *   php complete.php --key=h
 *   php complete.php --key=h --context=..
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
