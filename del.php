<?php
require_once("redis.php");
$uid = $_GET['id'];
$redis->delKey("user:" . $uid);

//删除链表中uid
$redis->lRem("uid", $uid);
header("location:index.php");