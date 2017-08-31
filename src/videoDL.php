<?php

$video_data = file_get_contents($argv[1]);

$url = explode("/", $argv[1]);
$file_name = $url[8];
file_put_contents('img/'.$file_name, $video_data);

exit(0);

?>