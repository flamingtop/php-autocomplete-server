<?php

require_once(__DIR__.'/BaseIndexTestCase.php');
require_once(__DIR__.'/../lib.php');

use \Autocomplete\APCIndex;

class APCIndexTest extends BaseIndexTestCase
{
    public function setUp() {
        $this->index = new APCIndex(); 
        $this->index->destroy();
    }
}