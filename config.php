<?php

namespace Autocomplete;

class Config
{
    /**
     * Index configuration used by rindex.php and complete.php  
     */  
    public static $index = array('type'=>'File', 'params'=>array('root' => '/tmp/testindex/'));
    // public static $indexer = array('type'=>'File', 'params'=>array('root'=>...));

    /**
     * @var string Use normal string to enable authentication
     */
    public static $indexerAuthKey = NULL;

}
