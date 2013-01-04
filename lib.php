<?php

namespace Autocomplete;

interface IndexStore
{
    /**
     * Low level call to load data from the index implementation
     * @param string $key
     * @return array mixed
     */  
    public function load($key);
    

    /**
     * Low level call to save data to the index implemenation
     * @param string $key
     * @param array $data
     * @abstract
     */
     public function save($key, $data);

    
    /**
     * Delete index record identified by key  
     *
     * @param string $key
     * @abstract
     */
     public function delete($key);

    
    /**
     * Delete everything in the index
     *
     * @abstract
     */
     public function destroy();
}

abstract class Index implements IndexStore
{

    /**
     * @var string Prefix applied to reverse index keys
     */
    private $context = '';


    /**
     * @return string
     */
    public function getContext() {
        return $this->context;
    }


    /**
     * @param string $context 
     */
    public function setContext($context) {
        $this->context = $context;
    }

    public static function create($type, array $options) {
        $class = __NAMESPACE__.'\\'.ucfirst($type).'Index';
        return new $class($options);
    }

    /**
     * Add an array of words into its index
     * 
     * @param array $words An array of UTF-8 encoded words
     */
    public function addWords($words) {
        foreach($words as $word) {
            is_string($word)
                && $this->addWord($word);
            is_array($word)
                && $this->addWord($word[0], $word[1]);
        }
    }


    /**
     * Add an array of words into another word's index
     *
     * Accepts 
     *   ['word1', 'word2' ...]
     *   ['word1' => x, 'word2' => y ...]
     * 
     * @param array $indexWord
     * @param array $words An array of UTF-8 encoded words
     */
    public function addWordsToWord($indexWord, $words) {
        foreach($words as $v) {
            is_string($v)
                && $this->addWordToWord($indexWord, $v);
            is_array($v)
                && $this->addWordToWord($indexWord, $v[0], $v[1]);
        }
    }
    

    /**
     * Add an UTF-8 encoded word into the index
     *
     * @param string $word @see index()
     * @score int $score @see index()
     */
    public function addWord($word, $score=0) {
        $rindex = $this->getReverseIndexKeys($word);
        foreach ($rindex as $key) {
            $this->indexWordByKey($key, $word, $score);
        }
    }


    /**
     * Add a word to another word's indexes
     *
     * @see indexWordByWord()
     *
     * @param string $word 
     * @param string $indexWord
     */
    public function addWordToWord($indexWord, $word, $score=0) {
        $this->indexWordByWord($indexWord, $word, $score);
    }


    /**
     * Increase word's score by $amount
     *
     * @param string $key
     * @param string $word
     * @param int $amount 
     * @return bool
     */  
    public function premoteWordByKey($key, $word, $amount=1) {
        $items = $this->get($key);
        $items[$word] += abs($amount);
        return $this->save($key, $items);
    }


    /**
     * Get index words by reverse index key
     *
     * @param string $key
     * @return array
     */
    public function byKey($key, $scores=false) {
        $key = $this->getContext() . $key;
        $items = $this->load($key);
        if(!is_array($items)) {
            return false;
        }
        return $scores ? $items : array_keys($items);
    }


    /**
     * Get indexes of a word
     *
     * @param string $word
     * @return array
     */
    public function byWord($word) {
        $rindexes = $this->getReverseIndexKeys($word);
        $indexes = array();        
        foreach ($rindexes as $key) {
            $items = $this->byKey($key, TRUE);
            $indexes[$key] = $items;
        }
        return $indexes;
    }


    /**
     * Index a word
     *
     * @param string $key The reverse index key
     * @param string $word The UTF-8 encoded word/phrase
     * @param int $score The hit rate of the word, the bigger the number the nearer the word appears in the auto-complete list
     * @return bool
     */  
    protected function indexWordByKey($key, $word, $score=0) {
        $items = $this->load($key);
        isset($items)
            || $items = array();
        isset($items[$word])
            || $items[$word] = $score;
        return $this->save($key, $items);
    }


    /**
     * Index a word by using another word's reversed index
     *
     * @param string $indexWord The word used to generate reverse indexes
     * @param string $word The UTF-8 encoded word/phrase
     * @param int $score The hit rate of the word, the bigger the number the nearer the word appears in the auto-complete list
     * @return bool
     */  
    protected function indexWordByWord($indexWord, $word, $score=0) {
        $rindex = $this->getReverseIndexKeys($indexWord);
        foreach ($rindex as $key) {
            $this->indexWordByKey($key, $word, $score);
        }
    }

    
    /**
     * Remove special characters in a word, except for space and dash
     * 
     * @param string $word
     * @return string
     */
    protected function sanitize($word) {
        return preg_replace("/[^\p{L} \-]+/u", "", $word);
    }


    /**
     * Figure out all reverse index keys of a given word
     *
     * @param string $word
     * @return array Array of reverse index keys
     */  
    Protected function getReverseIndexKeys($word) {
        $word = $this->sanitize($word);
        $rindex = array();
        for ($i=1; $i<mb_strlen($word, 'utf-8')+1; $i++) {
            $key = $this->getContext();
            $key .= mb_substr($word, 0, $i, 'utf-8');
            array_push($rindex, $key);
        }

        return $rindex;
    }
}

class APCIndex extends Index 
{
    public function load($key) {
        return apc_fetch($key);
    }
    
    public function save($key, $data) {
        return apc_store($key, $data);
    }

    public function delete($key) {
        return apc_delete($key);
    }

    public function destroy() {
        return apc_clear_cache('user');
    }
}

class FileIndex extends Index 
{
    
    /**
     * @var string
     */
    protected $root = NULL;


    public function __construct($opt=array()) {
        if(!file_exists($opt['root'])) {
            mkdir($opt['root']);
        }
        $this->root = $opt['root'];
    }


    /**
     * Return root directory
     *
     * @return string
     */
    public function getRoot() {
        return $this->root;
    }
    
    
    /**
     * Return storage path based on a key
     *
     * @param string $key
     * @return string Storage path of $key
     */
    public function getPath($key) {
        $hash = md5($key);
        $path = $this->getRoot().'/';
        $chars = str_split($hash);
        $path .= implode('/', array_slice($chars, 0, 10));
        if (!file_exists($path)) {
            mkdir($path, 0766, TRUE);
        }
        $path .= $hash;
        return $path;
    }
    

    public function load($key) {
        $path = $this->getPath($key);
        if(!file_exists($path)) {
            return FALSE;
        }
        return (array) json_decode(file_get_contents($path));
    }

    
    public function save($key, $data) {
        return file_put_contents($this->getPath($key), json_encode($data));
    }
    

    public function delete($key) {
        return unlink($this->getPath($key));
    }
    

    public function destroy() {
        $this->rrmdir($this->getRoot());
    }
    

    /**
     * Recursively remove everything in a directory
     *
     * @param string $dir Directory
     */
    private  function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") {
                        $this->rrmdir($dir."/".$object);
                    } else {
                        unlink($dir."/".$object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

}

