<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

if (!$user_id) {
    echo "<div class='showmag'><p>您还没有登录，不能执行此操作！</p><p><a href='index.php?op=login'>现在登陆</a></p></div>";
    wapfooter();
    exit;
}

if($action=="reply") {
    if ($user_id) {
        $replybody=iconv("utf-8", "gbk",daddslashes(trim($_POST['replybody'])));
        $toname=trim($_POST['toname']);
        $status_id=$_POST['status_id'];

        if ($replybody) {
            $toname=iconv("utf-8", "gbk",$toname);
            $toid=nametoid($toname);
            $replybody="@<a href=\"$webaddr/home/u.$toid\">$toname</a> $replybody";

            $res=$db->query("INSERT INTO et_content (user_id,content_body,posttime,type,status_id,status_uid,status_uname,status_type) VALUES ('$user_id','$replybody','$addtime','手机','$status_id','$toid','$toname','talk')");
            if ($res==1) {
                echo "<div class='showmag'><p>恭喜您，回复成功！</p><p><a href='index.php'>返回主页</a></p></div>";
                wapfooter();
                exit;
            } else {
                echo "<div class='showmag'><p>很抱歉，回复失败！</p><p><a href='index.php'>返回主页</a></p></div>";
                wapfooter();
                exit;
            }
        } else {
            echo "<div class='showmag'><p>回复失败，您没有填写回复的内容！</p><p><a href='index.php'>返回主页</a></p></div>";
            wapfooter();
            exit;
        }
    } else {
        echo "<div class='showmag'><p>回复失败，可能没有登陆成功或者浏览器不支持！</p><p><a href='index.php'>返回主页</a></p></div>";
        wapfooter();
        exit;
    }
}

$toname=$_GET["to"];
$status_id=$_GET['status_id'];
echo "<h2>给 $toname 回复</h2>".
"<form method=\"post\" action=\"index.php?op=reply\">".
"<p>@$toname <input type=\"text\" name=\"replybody\" value=\"\" maxlength=\"140\" /></p>".
"<p><input type=\"hidden\" name=\"toname\" value=\"$toname\" /><input type=\"hidden\" name=\"status_id\" value=\"$status_id\" /><input type=\"hidden\" name=\"action\" value=\"reply\" /><input type=\"submit\" value=\"发送\" /></p>".
"</form>";
?>