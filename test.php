<?php

require_once 'rc5.php';

$pw = "ExcelV444";
$km = "4567812131415165";

$rc5 = new RC5($km);

echo "\n";
$enc = $rc5->encrypt($pw);
// $dec = $rc5->decrypt($enc);
// echo $dec;
echo "\n";

?>