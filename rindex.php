<?php

// The indexer

require_once("config.php");
require_once("lib.php");
use \Autocomplete\Config;
use \Autocomplete\Index;


($authKey = filter_input(INPUT_POST, 'authKey'))
    || ($authKey = filter_input(INPUT_GET, 'authKey'))
    || ($authKey == Config::$indexerAuthKey)
    || die('Access denied');

$index = Index::create(Config::$index['type'], Config::$index['params']);

// $index->setContext('test');

($word = trim(filter_input(INPUT_POST, 'word')))
    || ($word = trim(filter_input(INPUT_GET, 'word')));

($words = trim(filter_input(INPUT_POST, 'words')))
    || ($words = trim(filter_input(INPUT_GET, 'words')));

($indexWord = trim(filter_input(INPUT_POST, 'indexWord')))
    || ($indexWord = trim(filter_input(INPUT_GET, 'indexWord')));

$word || $words || die('Give me something, like word=??? or words=???');

if($indexWord) {
    $word && $index->addWordToWord($indexWord, $word);
    $words && $index->addWordsToWord($indexWord, json_decode($words));
} else {
    $word && $index->addWord($word);
    $words && $index->addWords(json_decode($words));
}

echo 'OK';