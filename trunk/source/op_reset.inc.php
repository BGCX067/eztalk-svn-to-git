<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

tohome();

if ($action=="find") {
    $mailadres = daddslashes(trim($_POST["mailadres"]));

    if (mailtoid($mailadres)) {
        $seedstr =split(" ",microtime(),5);
        $seed =$seedstr[0]*10000;
        srand($seed);
        $pass =rand(10000,100000);
        $md5_pass=md5(md5($pass));
        $email_message_title=$webn1."迷你博客 找回密码";
        $message="您的新密码是: ".$pass."   请登陆后修改! Send by ".$webn1;

        $db->query("UPDATE et_users SET password = '$md5_pass' where mailadres='$mailadres'");

        include("include/mail.class.php");
        sendmail($mailadres,$email_message_title,$message);

        echo "<script>alert(\"提示：电子邮件已经发送到[$mailadres],请查收！\");location.href='$webaddr/op/reset';</script>";
        exit;
    }
    else {
        header("location: $webaddr/op/reset&tip=15");
        exit;
    }
}

//模板和Foot
$web_name3="找回密码";
$sqlnum=$db->querynum;
$mtime = explode(' ', microtime());
$loadtime=$mtime[1] + $mtime[0] - $starttime;
include($template->getfile('op_reset.htm'));
?>