<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

tologin();

if ($action=="auth") {
    $send=randStr(20);
    $send_2=base64_encode("$my[user_id]:$send");
    $send="����EasyTalk������֤�ʼ���
    �뽫�õ�ַ���Ƶ��������ַ����
    $webaddr/op/mailauth&act=authmail&auth=$send_2";

    $db->query("UPDATE et_users  SET auth_email='$send_2' where user_id='$my[user_id]'");

    include("include/mail.class.php");
    sendmail($my[mailadres],"EasyTalk ������֤",$send);

    echo "<script>alert(\"��ʾ����֤�ʼ��Ѿ����͵� $my[mailadres] ����ע����գ�\");window.location.href='$prev';</script>";
    exit;
}


if ($action=="edit") {
    $new_email= daddslashes(trim($_POST["email"]));
    $t=explode("@",$new_email);
    if(!$t[1]) {
        echo "<script>alert('����д�������ʽ����ȷ����������д��');location.href='$webaddr/op/mailauth'</script>";
        exit;
    }else{
        if ($new_email && $new_email!=$my[mailadres]) {
            $sql = "select mailadres from et_users where mailadres='$new_email'";
            $query = $db->query($sql);
            $row = $db->fetch_array($query);
            if ($row) {
                echo "<script>alert('����д�������Ѿ����ڣ�������������䣡');location.href='$webaddr/op/mailauth'</script>";
                exit;
            } else {
                $db->query("UPDATE et_users SET mailadres='$new_email',auth_email='0' where user_id='$my[user_id]'");
                header("location: $webaddr/op/login&act=logout&tip=6");
                exit;
            }
        } elseif ($new_email==$my[mailadres]){
            header("location: $webaddr/op/mailauth&tip=7");
            exit;
        } else {
            header("location: $webaddr/op/mailauth&tip=8");
            exit;
        }
    }
}

if ($act=="authmail") {
    $msg1=daddslashes($_GET['auth']);
    $msg=base64_decode($msg1);
    $tem=explode(":",$msg);
    $send_id=$tem[0];

    $sql = "select auth_email from et_users where user_id='$send_id'";
    $query = $db->query($sql);
    $row = $db->fetch_array($query);
    $auth_email=$row['auth_email'];

    if ($msg1==$auth_email) {
        $db->query("UPDATE et_users  SET auth_email='1' where user_id='$send_id'");
        header("location: $webaddr/op/mailauth&tip=9");
        exit;
    } else {
        header("location: $webaddr/op/mailauth&tip=10");
        exit;
    }
}

//ģ���Foot
$web_name3="������֤";
$sqlnum=$db->querynum;
$mtime = explode(' ', microtime());
$loadtime=$mtime[1] + $mtime[0] - $starttime;
include($template->getfile('op_mailauth.htm'));
?>