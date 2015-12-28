<?PHP
if(!defined('IN_ET')) {
	exit('Access Denied');
}

tologin();

if ($action=="account") {
    $pass= daddslashes(md5(md5($_POST["pass"])));
    $newpass1= daddslashes(md5(md5($_POST["newpass1"])));
    $newpass2= daddslashes(md5(md5($_POST["newpass2"])));
    if ($pass==$my['password']) {
        if ($newpass1!=$newpass2 || !trim($_POST["newpass1"])) {
            header("location: $webaddr/op/account&tip=16");
            exit;
        } else {
            $db->query("UPDATE et_users  SET password = '$newpass1' WHERE user_id='$my[user_id]'");
            header("location: $webaddr/op/login&tip=17");
            exit;
        }
    } else {
        header("location: $webaddr/op/account&tip=18");
        exit;
    }
}

//Ä£°åºÍFoot
$web_name3="ÐÞ¸ÄÃÜÂë";
$sqlnum=$db->querynum;
$mtime = explode(' ', microtime());
$loadtime=$mtime[1] + $mtime[0] - $starttime;
include($template->getfile('op_account.htm'));
?>