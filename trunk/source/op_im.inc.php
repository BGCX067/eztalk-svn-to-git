<?PHP
if(!defined('IN_ET')) {
	exit('Access Denied');
}

tologin();

if ($action=="msn") {
    $msn= $_POST["msn"];
	$send="yz".randStr(6);
	$msn_yz="$msn $send";
	$db->query("UPDATE et_users  SET msn = '$msn_yz' WHERE user_id='$my[user_id]'");
	echo "<script>alert(\"��ʾ���뷢����֤�루".$send."����MSN�����ˣ�".$msnrobot."���������֤��\");window.location.href='$prev';</script>";
	exit;
}

if ($action=="msnremove") {
    $db->query("UPDATE et_users  SET msn = '' WHERE user_id='$my[user_id]'");
    header("location: $webaddr/op/im");
    exit;
}

if ($action=="gettype") {
    $gettype= $_POST["gettype"];
    $db->query("UPDATE et_users  SET getmsgtype = '$gettype' WHERE user_id='$my[user_id]'");
    header("location: $webaddr/op/im");
    exit;
}

//ģ���Foot
$web_name3="IM��";
$sqlnum=$db->querynum;
$mtime = explode(' ', microtime());
$loadtime=$mtime[1] + $mtime[0] - $starttime;
include($template->getfile('op_im.htm'));
?>