<?php
function make_sql($status){
    $id = $status->id;
    $text = $status->text;
    $name = $status->user->name;
    $screen_name = $status->user->screen_name;
    $created_at = $status->created_at;
    $media_type = 'none';
    $media_url = ['', '', '', ''];
    $in_reply_to = $status->in_reply_to_status_id == null ? 0 : $status->in_reply_to_status_id;

    if(array_key_exists('extended_entities', $status)){
        $media_type = $status->extended_entities->media[0]->type;
        $count = 0;
        foreach ($status->extended_entities->media as $media){
            switch ($media->type) {
                case 'photo':
                    $media_url[$count] = $media->media_url ."\n\r";
                    break;
                case 'animated-gif':
                    $media_url[$count] = $media->video_info->variants[0]->url ."\n\r";
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

                    $media_url[$count] = $media->video_info->variants[$key]->url ."\r\n";
                    break;
            }
            $count++;
        }
    }
    
    return "INSERT IGNORE INTO tweet VALUES ($id, '".mysql_escape_string($text)."', '".mysql_escape_string($name)."', '". $screen_name.
            "', '". date("Y-m-d H:i:s", strtotime($created_at))."', '". $media_type."', '". mysql_escape_string($media_url[0]).
            "', '". mysql_escape_string($media_url[1])."', '". mysql_escape_string($media_url[2]).
            "', '". mysql_escape_string($media_url[3])."', ".$in_reply_to.")";
}
?>