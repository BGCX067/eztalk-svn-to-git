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
	echo "<script>alert(\"提示：请发送验证码（".$send."）到MSN机器人（".$msnrobot."）来获得验证！\");window.location.href='$prev';</script>";
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

//模板和Foot
$web_name3="IM绑定";
$sqlnum=$db->querynum;
$mtime = explode(' ', microtime());
$loadtime=$mtime[1] + $mtime[0] - $starttime;
include($template->getfile('op_im.htm'));
?>