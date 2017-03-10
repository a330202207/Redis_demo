<?php
require_once("redis.php");
$id  = $_GET['id'];
$uid = $_GET['uid'];

$redis->sAdd("user:" . $uid . ":following", $id);
$redis->sAdd("user:" . $id . ":followers", $uid);
header("location:index.php");