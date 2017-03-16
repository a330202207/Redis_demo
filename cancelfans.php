<?php
require_once("redis.php");
$id  = $_GET['id'];
$uid = $_GET['uid'];


$redis->sRem("user:" . $uid . ":following", $id);
$redis->sRem("user:" . $id . ":followers", $uid);
header("location:index.php");