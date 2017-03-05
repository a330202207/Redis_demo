<?php
require_once("redis.php");

$username = $_POST['username'];
$pass     = $_POST['password'];

$id  = $redis->getKey("username:" . $username);

if (!empty($id)) {

    //获取user哈希表中用户的密码
    $password = $redis->hGet("user:".$id , 'password');

    if (md5($pass) == $password) {

        $auth = md5(time() . $username . rand());

        $redis->setKey("auth:" . $auth, $id);

        //设置cookie时间
        setcookie("auth", $auth, time() + 86400);
        header("location:index.php");
    }
}