<?php
require_once("redis.php");

$username = $_POST['username'];
$password = md5($_POST['password']);

//userid自增
$uid = $redis->incr("userid");
$redis->hMset("user:" . $uid, array("uid" => $uid, 'username' => $username, "password" => $password));

//用户id存入列表
$redis->rpush("uid", $uid);
$redis->set("username:" . $username, $uid);

$auth = md5(time() . $username . rand());

$redis->set("auth:" . $auth, $uid);

//设置cookie时间
setcookie("auth", $auth, time() + 86400);
header("location:index.php");
