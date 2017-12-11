<?php

require_once 'rc5.php';

$pw = "ExcelV44";
$km = "4567812131415165";

$rc5 = new RC5($km);

echo "\n";
$enc = $rc5->encrypt($pw);
$rc5->decrypt($enc);
echo "\n";

?>