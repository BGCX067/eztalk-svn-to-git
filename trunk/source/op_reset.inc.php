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
        $email_message_title=$webn1."���㲩�� �һ�����";
        $message="������������: ".$pass."   ���½���޸�! Send by ".$webn1;

        $db->query("UPDATE et_users SET password = '$md5_pass' where mailadres='$mailadres'");

        include("include/mail.class.php");
        sendmail($mailadres,$email_message_title,$message);

        echo "<script>alert(\"��ʾ�������ʼ��Ѿ����͵�[$mailadres],����գ�\");location.href='$webaddr/op/reset';</script>";
        exit;
    }
    else {
        header("location: $webaddr/op/reset&tip=15");
        exit;
    }
}

//ģ���Foot
$web_name3="�һ�����";
$sqlnum=$db->querynum;
$mtime = explode(' ', microtime());
$loadtime=$mtime[1] + $mtime[0] - $starttime;
include($template->getfile('op_reset.htm'));
?>