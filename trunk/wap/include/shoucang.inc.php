<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

if (!$user_id) {
    echo "<div class='showmag'><p>您还没有登录，不能执行此操作！</p><p><a href='index.php?op=login'>现在登陆</a></p></div>";
    wapfooter();
    exit();
}

$sid=$_GET['sid'];
$sql = "select fav_id from et_favorite where content_id='$sid'";
$query = $db->query($sql);
$data = $db->fetch_array($query);
if (!$data['fav_id'] && $user_id) {
    $sql = "select content_id from et_content where content_id='$sid'";
    $query = $db->query($sql);
    $data = $db->fetch_array($query);
    $content_id=$data['content_id'];

    $db->query("INSERT INTO et_favorite (content_id,sc_uid) VALUES ('$content_id','$user_id')");
    $db->query("UPDATE et_users SET fav_num=fav_num+'1' where user_id='$user_id'");
}
echo "<div class='showmag'><p>收藏成功！</p><p><a href='index.php'>返回主页</a></p></div>";
wapfooter();
exit();
?>