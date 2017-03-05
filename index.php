<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>管理界面</title>
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
</head>
<body>
<div id="panelwrap">

    <div class="header">
        <div class="title"><a href="#">管理界面</a></div>
        <?php
        require_once("redis.php");
        if (!empty($_COOKIE['auth'])) {
        $id = $redis->getKey("auth:" . $_COOKIE['auth']);
        $name = $redis->hGet("user:" . $id, "username");
        ?>
            <div class="header_right">欢迎您！ <?php echo $name ?>,<a href="logout.php" class="logout">注销</a>
        <?php
        }
        ?>
        </div>

        <div class="menu">
            <ul>
                <li><a href="#" class="selected">菜单</a></li>
            </ul>
        </div>

    </div>

    <div class="submenu">
        <ul>
            <li><a href="#" class="selected">用户管理</a></li>
        </ul>
    </div>

    <?php

    //用户总数
    $count = $redis->lLen("uid");

    //页大小
    $page_size = 3;


    //当前页码
    $page_num = (!empty($_GET['page'])) ? $_GET['page'] : 1;

    //页总数
    $page_count = ceil($count / $page_size);

    /*
    *		     页码
    * lrange 0 2  1
    * lrange 3 5  2
    * lrange 6 8  3
    */

    $ids = $redis->lrangeList("uid", ($page_num - 1) * $page_size, (($page_num - 1) * $page_size + $page_size - 1));

    foreach ($ids as $v) {
        $data[] = $redis->hGetAll("user:" . $v);
    }
    ?>

    <div class="center_content">

        <div id="right_wrap">
            <div id="right_content">
                <h2>用户列表</h2>

                <table id="rounded-corner">
                    <thead>
                    <tr>
                        <th><input type="checkbox" id="CheckedAll" name=""/></th>
                        <th>ID</th>
                        <th>用户名</th>
                        <th>操作</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <td colspan="12">
                            <a href="?page=1">首页</a>
                            <a href="?page=<?php echo (($page_num - 1) <= 1) ? 1 : ($page_num - 1) ?>">上一页</a>
                            <a href="?page=<?php echo (($page_num + 1) >= $page_count) ? $page_count : ($page_num + 1) ?>">下一页</a>
                            <a href="?page=<?php echo $page_count ?>">尾页</a>
                            <span style="float:right">
                                当前<?php echo $page_num ?>页
                                总共<?php echo $page_count ?>页
                                总共<?php echo $count ?>个用户
                            </span>
                        </td>
                    </tr>

                    </tfoot>
                    <tbody>
                    <?php foreach ($data as $v){ ?>
                        <tr class="odd">
                            <td><input type="checkbox" name="user_id" value="<?php echo $v['uid'] ?>"/></td>
                            <td><?php echo $v['uid'] ?></td>
                            <td><?php echo $v['username'] ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $v['uid'] ?>"><img src="images/edit.png" border="0"/></a>
                                <a href="del.php?id=<?php echo $v['uid'] ?>"><img src="images/trash.gif" border="0"/></a>
                            </td>
                            <td>加关注/取消关注</a></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>

                <div class="form_sub_buttons">
                    <input type="button" class="follow" value="关注">
                    <input type="button" class="del" value="删除">
                    <input type="button" class="edit" value="编辑">
                </div>
            </div>
        </div><!-- end of right content-->


        <div class="sidebar" id="sidebar">
            <h2>我关注了谁</h2>
            <ul>
                <?php
                $data = $redis->sMembers("user:" . $id . ":following");
                foreach ($data as $v) {
                    $row = $redis->hGetAll("user:" . $v);
                ?>
                    <li><a href="#"><?php echo $row['username'] ?></a></li>
                <?php
                }
                ?>

            </ul>
            <h2>谁关注了我</h2>
            <ul>
                <?php
                $data = $redis->sMembers("user:" . $id . ":followers");
                foreach ($data as $v) {
                $row = $redis->hGetAll("user:" . $v);
                ?>
                <li><a href="#"><?php echo $row['username'] ?></a></li>
            </ul>
                <?php
                }
                ?>

        </div>


        <div class="clear"></div>
    </div> <!--end of center_content-->

    <div class="footer">
    </div>
</div>
</body>
<script src="js/jquery-3.1.1.min.js"></script>
<script>
    $("#CheckedAll").click(function () {
        $('[name=user_id]:checkbox').attr("checked", this.checked);
    });
    $("[name=user_id]:checked").click(function () {
        var tmp = $("[name=user_id]:checkbox");
        $("#checkAll").attr("checked", tmp.length == tmp.filter(':checked'.length))
    });
    $(".del").click(function (e) {

        var ids = "";

        var data = $("[name=user_id]:checked").each(function () {
            ids += "," + $(this).val();
        });

        ids.substr(1);

        if (ids == '' || ids == undefined) {
            alert("请选择后再操作！");
        }

        window.location.href = location.protocol +"del.php?id=";

    });
</script>
</html>