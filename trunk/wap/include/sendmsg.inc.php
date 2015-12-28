<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

if (!$user_id) {
    echo "<div class='showmag'><p>您还没有登录，不能执行此操作！</p><p><a href='index.php?op=login'>现在登陆</a></p></div>";
    wapfooter();
    exit;
}

$senduid=$_GET['uid'];
$sendname=idtoname($senduid);

if ($action=="post") {
    $msgbody=iconv("utf-8", "gbk",replace(daddslashes($_POST['msgbody'])));

	$tm=$db->query("INSERT INTO et_messages (js_id,fs_id,message_body,m_time) VALUES ('$senduid','$user_id','$msgbody','$addtime')");
    if($tm==1) {
        echo "<div class='showmag'><p>给".$sendname."的私信发送成功</p><p><a href='index.php'>返回首页</a></p></div>";
        wapfooter();
        exit;
    } else {
        echo "<div class='showmag'><p>很抱歉，私信发送失败！</p><p><a href='index.php'>返回首页</a></p></div>";
        wapfooter();
        exit;
    }
}

echo "<h2>给 $sendname 发送私信</h2>".
"<form method=\"post\" action=\"index.php?op=sendmsg&uid=$senduid\">".
"<p><input type=\"text\" name=\"msgbody\" value=\"\" maxlength=\"140\" /></p>".
"<p><input type=\"hidden\" name=\"action\" value=\"post\" /><input type=\"submit\" value=\"发送\" /></p>".
"</form>";
?>