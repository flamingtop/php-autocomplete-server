<?php

require_once(__DIR__.'/BaseIndexTestCase.php');
require_once(__DIR__.'/../lib.php');

use \Autocomplete\FileIndex;

class FileIndexTest extends BaseIndexTestCase
{
    public function setUp() {
        $this->index = new FileIndex(array('root' => '/tmp/autocomplete-index-test'));
        $this->index->destroy();
    }
}