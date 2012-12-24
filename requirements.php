<?php

must(phpversion() >= 5.4, "PHP v5.4");
must(extension_loaded('mbstring'), "mbstring extension");




function e($s) { echo $s . PHP_EOL;  }
function must($bool_expr, $msg) { $bool_expr ? e($msg.' OK') : e($msg.' is required.'); }
