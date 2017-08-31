<?php
require_once 'keys.php';
require_once 'makeSQL.php';
require_once 'twitteroauth/autoload.php';

use Abraham\TwitterOAuth\TwitterOAuth;

$conn = mysql_connect('localhost', 'user1', 'user1');
if(!$conn) {
    die("Connection Failed.\r\n".mysql_error());
}
$db = mysql_select_db('favorites');
if(!$db){
    die("DB Select Failed.\r\n".mysql_error());
}
mysql_set_charset('utf8');

$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN ,ACCESS_TOKEN_SECRET);
$user = $connection->get("account/verify_credentials");
$favorites;
$max_id;
$fav_count = $user->favourites_count;
$loop_end = ($fav_count/200)+1;

echo "GET favorites beginning...\r\n";

for($loop=1; $loop <= (int)$loop_end; $loop++){
    if($loop==1){
        $favorites = $connection->get("favorites/list", ["count"=>200]);
        echo "Start Parsing... \r\n";
        $max_id = $favorites[count($favorites)-1]->id;
        favs_parse($favorites);
    } else {
        $favorites = $connection->get("favorites/list", ["count"=>200, "max_id"=>$max_id]);
        echo "Start Parsing after $max_id\r\n";
        $max_id = $favorites[count($favorites)-1]->id;
        favs_parse($favorites);
    }
}

echo "Closing MySQL Connection...\r\n";
mysql_close($conn);

exit(0);

function favs_parse($favorites){
    foreach ($favorites as $status) {
        print $status->user->name ." (@". $status->user->screen_name .")" ."\n\r";
        print $status->text ."\n\r";

        if(array_key_exists('extended_entities', $status)) {
            foreach ($status->extended_entities->media as $media) {
                media_parse($media);
            }
        }
        print $status->created_at ."\n\r\n\r";
        
        echo "Start Inserting to mySQL...\r\n";
        $query = make_sql($status);
        echo $query."\r\n";
        $result = mysql_query($query);
        if(!$result){
            die("INSERT Failed.\r\n".mysql_error());
        }
    }
}

function media_parse($media){
    switch ($media->type) {
        case 'photo':
            popen("start /b php imageDL.php ".$media->media_url, "r");
            print $media->media_url ."\n\r";
            break;
        case 'animated-gif':
            popen("start /b php agifDL.php ".$media->video_info->variants[0]->url, "r");
            print $media->video_info->variants[0]->url ."\n\r";
            break;
        case 'video':
            $video_bitrates = array();
            foreach ($media->video_info->variants as $video){
                if(array_key_exists('bitrate', $video)){
                    array_push($video_bitrates, $video->bitrate);
                } else {
                    array_push($video_bitrates, 0);
                }
            }

            $max = max($video_bitrates);
            $arrFind = array_keys($video_bitrates, $max);
            $key = $arrFind[0];

            popen("start /b php videoDL.php ".$media->video_info->variants[$key]->url, "r");
            print $media->video_info->variants[$key]->url ."\r\n";
            //var_dump($media);
            break;
    }
}

?>