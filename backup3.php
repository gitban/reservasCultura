<?php 

error_reporting(E_ALL);
echo 1;
$salida = shell_exec('php');
echo 2;
echo "<pre>$salida</pre>";
echo 3;

?>