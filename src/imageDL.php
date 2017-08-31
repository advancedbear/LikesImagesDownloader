<?php

$image_data = file_get_contents($argv[1].":orig");
$file_name = substr($argv[1], 28);
file_put_contents('img/'.$file_name, $image_data);

exit(0);

?>