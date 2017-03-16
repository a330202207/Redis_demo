<?php
require_once("redis.php");
$uids = explode(',', $_GET['id']);

foreach ($uids as $key) {
    $redis->del("user:" . $key);
    //删除链表中uid
    $redis->lRem("uid", $key);
}

header("location:index.php");