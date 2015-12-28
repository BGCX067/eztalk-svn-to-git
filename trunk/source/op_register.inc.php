<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

tohome();

if (file_exists(ET_ROOT."/include/syst/isreg.syst")) {
    echo "<script>alert('您好，很抱歉，EasyTalk 管理员关闭了在线注册！');location.href='$webaddr/index'</script>";
    exit;
}

$username=iconv("utf-8", "gbk",daddslashes(trim($_GET['uname'])))?iconv("utf-8", "gbk",daddslashes(trim($_GET['uname']))):daddslashes(trim($_GET['uname']));
$mailadres=iconv("utf-8", "gbk",daddslashes(trim($_GET['mail'])))?iconv("utf-8", "gbk",daddslashes(trim($_GET['mail']))):daddslashes(trim($_GET['mail']));
$pass1=daddslashes(trim($_GET['pass1']));
$pass2=daddslashes(trim($_GET['pass2']));

if ($act=="check") {
    // 用户名
    if(!$username) {
        echo "请填写您的用户名!";
        exit;
    }
    if(StrLenW($username)>16 || StrLenW($username)<4) {
        echo "用户名长度应该大于4小于16个字符！";
        exit;
    }
    $sql = "select user_id from et_users where user_name='$username'";
	$query = $db->query($sql);
    if ($db->fetch_array($query)) {
        echo "用户名已存在，不能使用!";
        exit;
    }
    // 电子邮件
    if(!$mailadres) {
        echo "请填写电子邮件地址!";
        exit;
    }
    $t=explode("@",$mailadres);
    if(!$t[1]) {
        echo "电子邮件格式不正确！";
        exit;
    }
    $sql = "select user_id from et_users where mailadres='$mailadres'";
	$query = $db->query($sql);
    if ($db->fetch_array($query)) {
        echo "此电子邮件已存在，不能使用!";
        exit;
    }
    // 密码
    if (StrLenW($pass1)<6 || StrLenW($pass1)>32) {
        echo "密码长度不能小于6位!";
        exit;
    }
    if ($pass1!=$pass2) {
        echo "两次输入的密码不一致!";
        exit;
    }
    echo "check_ok";
    exit;
}

if ($act=="reg") {
    if ($username && $mailadres && $pass1==$pass2) {
        $t=$db->query("INSERT INTO et_users (user_name,password,mailadres,signupdate) VALUES ('$username','".md5(md5($pass2))."','$mailadres','$addtime')");
        $regid=mysql_insert_id();
        if($t==1 && $regid) {
            $db->query("INSERT INTO et_friend (fid_jieshou,fid_fasong) VALUES ('1','$regid')");
            $db->query("INSERT INTO et_friend (fid_jieshou,fid_fasong) VALUES ('$regid','1')");
            dsetcookie('authcookie', authcode(md5(md5($pass2))."\t$mailadres\t$regid",'ENCODE'));
            echo "reg_ok";
            exit;
        }else{
            echo "注册失败，可能因为您没有填写完整注册信息，请重新填写！";
            exit;
        }
    } else {
        echo "注册失败，可能因为您没有填写完整注册信息，请重新填写！";
        exit;
    }
}

//模板和Foot
$web_name3="新用户注册";
$sqlnum=$db->querynum;
$mtime = explode(' ', microtime());
$loadtime=$mtime[1] + $mtime[0] - $starttime;
include($template->getfile('op_register.htm'));
?>
