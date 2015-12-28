<?PHP
define('is_admin_path', 'yes');
include('../common.inc.php');

if ($admin_login !="yes" || $my[isadmin]!=1)
    header("Location: admin_login.php");

if ($action=="send") {
    $send_uid=trim($_POST['uid']);
    $send_msg=$_POST['msg'];
    $send_msg=eregi_replace('<A .*HREF=("|\')?([^ "\']*)("|\')?.*>([^<]*)</A>', '\4', $send_msg);
    $send_msg=daddslashes("<font color=red>[群发]</font> ".$send_msg);
    $send_tem=explode(",",$send_uid);
    $uid_count=count($send_tem);

    if (!$send_uid) {
        echo "<script>alert(\"提示：您没有填写发送的 UID!\");window.location.href='sendmsg.php';</script>";
        exit;
    }
	if ($send_uid=="ALL") {
		$sql = "select user_id from et_users order by user_id desc limit 1";
		$query = $db->query($sql);
		$row = $db->fetch_array($query);
		$max_uid=$row['user_id'];
		for ($i=1;$i<=$max_uid;$i++) {
			$db->query("INSERT INTO et_messages (js_id,fs_id,message_body,m_time) VALUES ('$i','1','$send_msg','$addtime')");
		}
        echo "<script>alert(\"提示：群发信息已经发送!\");window.location.href='sendmsg.php';</script>";
        exit;
	} else {
		for ($i=0;$i<$uid_count;$i++) {
			$db->query("INSERT INTO et_messages (js_id,fs_id,message_body,m_time) VALUES ('".$send_tem[$i]."','1','$send_msg','$addtime')");
		}
        echo "<script>alert(\"提示：群发信息已经发送!\");window.location.href='sendmsg.php';</script>";
        exit;
	}
}

include($template->getfile('sendmsg.htm'));
?>