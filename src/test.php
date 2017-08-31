<?php
$conn = mysql_connect('localhost', 'user1', 'user1');
if(!$conn) {
    die("Connection Failed.\r\n".mysql_error());
}
$db = mysql_select_db('favorites');
if(!$db){
    die("DB Select Failed.\r\n".mysql_error());
}

mysql_query("set names utf8");

$query = 'INSERT INTO tweet VALUES (100, "テスト", "くまもっち", "advanced_bear", "Thu Aug 31 01:55:51 +0000 2017", "Photo", "http://pbs.twimg.com/media/DIe6KDzVwAYe7d4.jpg", "", "", "", "")';
$result = mysql_query($query);
if(!$result){
    die("INSERT Failed.\r\n".mysql_error());
}

$result = mysql_query("SELECT * FROM tweet");
if(!$result){
    die("INSERT Failed.\r\n".mysql_error());
}
var_dump(mysql_fetch_array($result));


mysql_close($conn);
?>