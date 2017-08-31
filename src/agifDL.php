<?php

$agif_data = file_get_contents($argv[1]);

$url = explode("/", $argv[1]);
$file_name = $url[4];
file_put_contents('img/'.$file_name, $agif_data);

exit(0);

?>