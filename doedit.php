<?php
require_once("redis.php");
$uid      = $_POST['uid'];
$username = $_POST['username'];
$password = md5($_POST['password']);
$result   = $redis->hMset("user:" . $uid, array("username" => $username, "password" => $password));
if ($result) {
    header("location:index.php");
} else {
    header("location:edit.php?id=" . $uid);
}