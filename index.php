<?php

/**
 * @file
 * The indexer
 * 
 */

require_once("config.php");
require_once("lib.php");
use \Autocomplete\Config;
use \Autocomplete\Index;

$word = $words = $indexWord = $context = NULL;

if (php_sapi_name() == 'cli') {

    $opt = getopt('', array('word:', 'words:', 'indexWord::',  'context::'));
    isset($opt['word']) && $word = $opt['word'];
    isset($opt['words']) && $words = $opt['words'];
    isset($opt['indexWord']) && $indexWord = $opt['indexWord'];
    isset($opt['context']) && $context = $opt['context'];
  
} else {
  
    ($authKey = filter_input(INPUT_POST, 'authKey'))
        || ($authKey = filter_input(INPUT_GET, 'authKey'))
        || ($authKey == Config::$indexerAuthKey)
        || die('Access denied');

    ($word = trim(filter_input(INPUT_POST, 'word')))
        || ($word = trim(filter_input(INPUT_GET, 'word')));

    ($words = trim(filter_input(INPUT_POST, 'words')))
        || ($words = trim(filter_input(INPUT_GET, 'words')));

    ($indexWord = trim(filter_input(INPUT_POST, 'indexWord')))
        || ($indexWord = trim(filter_input(INPUT_GET, 'indexWord')));

    ($context = trim(filter_input(INPUT_POST, 'context')))
        || ($indexWord = trim(filter_input(INPUT_GET, 'context')));

}

$word || $words || die('Give me something, like word=??? or words=???');

(strpos($word, ':') !== FALSE) && $word = explode(':', $word);
$words && $words = explode(',', $words);
if ($words) {
    foreach($words as $k=>$v) {
        if(strpos($v, ':') !== FALSE) {
            $words[] = explode(':', $v);
            unset($words[$k]);
        }
    }
}

$index = Index::create(Config::$index['type'], Config::$index['params']);
$context && $index->setContext($context);


if($indexWord) {
    $word && $index->addWordToWord($indexWord, $word);
    $words && $index->addWordsToWord($indexWord, $words);
} else {
    $word && $index->addWord($word);
    $words && $index->addWords($words);
}

echo 'OK';