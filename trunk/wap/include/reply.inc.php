<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

if (!$user_id) {
    echo "<div class='showmag'><p>����û�е�¼������ִ�д˲�����</p><p><a href='index.php?op=login'>���ڵ�½</a></p></div>";
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

            $res=$db->query("INSERT INTO et_content (user_id,content_body,posttime,type,status_id,status_uid,status_uname,status_type) VALUES ('$user_id','$replybody','$addtime','�ֻ�','$status_id','$toid','$toname','talk')");
            if ($res==1) {
                echo "<div class='showmag'><p>��ϲ�����ظ��ɹ���</p><p><a href='index.php'>������ҳ</a></p></div>";
                wapfooter();
                exit;
            } else {
                echo "<div class='showmag'><p>�ܱ�Ǹ���ظ�ʧ�ܣ�</p><p><a href='index.php'>������ҳ</a></p></div>";
                wapfooter();
                exit;
            }
        } else {
            echo "<div class='showmag'><p>�ظ�ʧ�ܣ���û����д�ظ������ݣ�</p><p><a href='index.php'>������ҳ</a></p></div>";
            wapfooter();
            exit;
        }
    } else {
        echo "<div class='showmag'><p>�ظ�ʧ�ܣ�����û�е�½�ɹ������������֧�֣�</p><p><a href='index.php'>������ҳ</a></p></div>";
        wapfooter();
        exit;
    }
}

$toname=$_GET["to"];
$status_id=$_GET['status_id'];
echo "<h2>�� $toname �ظ�</h2>".
"<form method=\"post\" action=\"index.php?op=reply\">".
"<p>@$toname <input type=\"text\" name=\"replybody\" value=\"\" maxlength=\"140\" /></p>".
"<p><input type=\"hidden\" name=\"toname\" value=\"$toname\" /><input type=\"hidden\" name=\"status_id\" value=\"$status_id\" /><input type=\"hidden\" name=\"action\" value=\"reply\" /><input type=\"submit\" value=\"����\" /></p>".
"</form>";
?>