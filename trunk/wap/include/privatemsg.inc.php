<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

if (!$user_id) {
    echo "<div class='showmag'><p>您还没有登录，不能执行此操作！</p><p><a href='index.php?op=login'>现在登陆</a></p></div>";
    wapfooter();
    exit();
}

$pmtp=$_GET['pmtp']?$_GET['pmtp']:"my";

//导航
if ($pmtp=="my") echo "<h2>我收到的私信 | <a href='index.php?op=privatemsg&pmtp=send'>我发出的私信</a></h2>";
else if ($pmtp=="send") echo "<h2><a href='index.php?op=privatemsg'>我收到的私信</a> | 我发出的私信</h2>";

//删除确认
if($act=="delpmsg") {
    $mid = trim($_GET["mid"]);
    echo "<div class='showmag'><p>是否确认删除此条私信？</p><p><a href='index.php?op=privatemsg&act=delpmsgok&mid=$mid'>确认</a> <a href='index.php?op=privatemsg'>取消</a></p></div>";
    wapfooter();
    exit();
}

//删除OK
if($act=="delpmsgok") {
    $mid = trim($_GET["mid"]);
    $tm=$db->query("DELETE FROM et_messages WHERE (js_id ='$user_id' || fs_id='$user_id') && message_id='$mid'");
    if ($tm==1) {
        echo "<div class='showmag'><p>恭喜您，私信删除成功！</p><p><a href='index.php?op=privatemsg'>返回私信页面</a></p></div>";
        wapfooter();
        exit();
    } else {
        echo "<div class='showmag'><p>很抱歉，私信删除失败！</p><p><a href='index.php?op=privatemsg'>返回私信页面</a></p></div>";
        wapfooter();
        exit();
    }
}

//查询
$start= ($page-1)*10;
if ($pmtp=="my") {
    $sql = "select m.message_id,m.message_body,m.m_time,u.user_id,u.user_name from et_messages AS m,et_users AS u where m.fs_id=u.user_id && m.js_id='$user_id' order by m.message_id desc limit $start,10";
} else {
    $sql = "select m.message_id,m.message_body,m.m_time,u.user_id,u.user_name from et_messages AS m,et_users AS u where m.js_id=u.user_id && m.fs_id='$user_id' order by m.message_id desc limit $start,10";
}
$query = $db->query($sql);
while ($data=$db->fetch_array($query)) {
    $i=$i+1;
    $contentid=$data['message_id'];
    $home_uid=$data['user_id'];
    $home_uname=$data['user_name'];
    $content = str_replace("home/u.","wap/index.php?op=home&uid=",$data['message_body']);
    $content=urlreplace($content);
    $posttime=timeop($data['m_time']);

    if ($pmtp=="my") echo "<li>来自:";
    else echo "<li>发给:";
    echo "<a href='index.php?op=home&uid=$home_uid'>$home_uname</a> $content <span class=\"stamp\">$posttime <a href=\"index.php?op=privatemsg&act=delpmsg&mid=$contentid\">删除</a></span></li>";
}
echo "</ul>";

//分页
if ($pmtp=="my") {
    $query = $db->query("select count(*) as count from et_messages AS m,et_users AS u where m.fs_id=u.user_id && m.js_id='$user_id'");
} else {
    $query = $db->query("select count(*) as count from et_messages AS m,et_users AS u where m.js_id=u.user_id && m.fs_id='$user_id'");
}
$row = $db->fetch_array($query);
$total=$row['count'];
$pg_num=$total/10;
$pg_num=intval($pg_num);
if ($total!=$pg_num*10) $pg_num=$pg_num+1;
$np=$page+1;
$pp=$page-1;
if ($pg_num>1) {
    echo "<div class='page'>";
    if ($pp>0) echo "<a href='index.php?op=privatemsg&pmtp=$pmtp&page=$pp'>上页</a>&nbsp;";
    if ($np<=$pg_num) echo "<a href='index.php?op=privatemsg&pmtp=$pmtp&page=$np'>下页</a>&nbsp;";
    echo "| ".$page."/".$pg_num;
    echo "</div>";
}
?>