<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

if (!$user_id) {
    echo "<div class='showmag'><p>����û�е�¼������ִ�д˲�����</p><p><a href='index.php?op=login'>���ڵ�½</a></p></div>";
    wapfooter();
    exit;
}

$senduid=$_GET['uid'];
$sendname=idtoname($senduid);

if ($action=="post") {
    $msgbody=iconv("utf-8", "gbk",replace(daddslashes($_POST['msgbody'])));

	$tm=$db->query("INSERT INTO et_messages (js_id,fs_id,message_body,m_time) VALUES ('$senduid','$user_id','$msgbody','$addtime')");
    if($tm==1) {
        echo "<div class='showmag'><p>��".$sendname."��˽�ŷ��ͳɹ�</p><p><a href='index.php'>������ҳ</a></p></div>";
        wapfooter();
        exit;
    } else {
        echo "<div class='showmag'><p>�ܱ�Ǹ��˽�ŷ���ʧ�ܣ�</p><p><a href='index.php'>������ҳ</a></p></div>";
        wapfooter();
        exit;
    }
}

echo "<h2>�� $sendname ����˽��</h2>".
"<form method=\"post\" action=\"index.php?op=sendmsg&uid=$senduid\">".
"<p><input type=\"text\" name=\"msgbody\" value=\"\" maxlength=\"140\" /></p>".
"<p><input type=\"hidden\" name=\"action\" value=\"post\" /><input type=\"submit\" value=\"����\" /></p>".
"</form>";
?>