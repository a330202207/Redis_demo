<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>管理界面</title>
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
</head>
<body>
<?php
require_once("redis.php");
$uid = $_GET['id'];
$data = $redis->hGetAll("user:" . $uid);
?>

<form action="doedit.php" method="post">
    <div id="loginpanelwrap">
        <div class="loginheader">
            <div class="logintitle"><a href="">注册</a></div>
        </div>
        <div class="loginform">
            <div class="loginform_row">
                <label>用户名:</label>
                <input type="text" class="loginform_input" name="username" value="<?php echo $data['username'] ?>"/>
            </div>
            <div class="loginform_row">
                <label>密码:</label>
                <input type="text" class="loginform_input" name="password"/>
            </div>
            <div class="loginform_row">
                <input type="hidden" value="<?php echo $data['uid'] ?>" name="uid"/>
                <input type="reset" class="register_reset" value="重置"/>
                <input type="button" class="register_submit" value="确定"/>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</form>
</body>
</html>