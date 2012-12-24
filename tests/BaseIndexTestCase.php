<?php

class BaseIndexTestCase extends PHPUnit_Framework_TestCase
{

    protected $index = NULL;

    /**
     * @dataProvider data1
     */
    public function testAddWord($word, $result) {
        $this->index->addWord($word);
        $this->assertEquals($result, $this->index->byWord($word));
    }

    /**
     * @dataProvider data2
     */
    public function testAddWordToWord($indexWord, $word, $result) {
        $this->index->addWordToWord($indexWord, $word);
        $this->assertEquals($result, $this->index->byWord($indexWord));
    }

    /**
     * @dataProvider data3
     */
    public function testAddWords($words, $result) {
        $this->index->addWords($words);
        foreach($words as $word) {
            $this->assertEquals($result[$word], $this->index->byWord($word));
        }
    }

    /**
     * @dataProvider data4
     */
    public function testAddWordsToWord($indexWord, $words, $result) {
        $this->index->addWordsToWord($indexWord, $words);
        $this->assertEquals($this->index->byWord($indexWord), $result);
    }
    
    public function data1() {
        return require(__DIR__."/data/data1.inc");
    }
    public function data2() {
        return require(__DIR__."/data/data2.inc");
    }
    public function data3() {
        return require(__DIR__."/data/data3.inc");
    }
    public function data4() {
        return require(__DIR__."/data/data4.inc");
    }

}

