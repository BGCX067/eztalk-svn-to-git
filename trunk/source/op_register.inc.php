<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

tohome();

if (file_exists(ET_ROOT."/include/syst/isreg.syst")) {
    echo "<script>alert('���ã��ܱ�Ǹ��EasyTalk ����Ա�ر�������ע�ᣡ');location.href='$webaddr/index'</script>";
    exit;
}

$username=iconv("utf-8", "gbk",daddslashes(trim($_GET['uname'])))?iconv("utf-8", "gbk",daddslashes(trim($_GET['uname']))):daddslashes(trim($_GET['uname']));
$mailadres=iconv("utf-8", "gbk",daddslashes(trim($_GET['mail'])))?iconv("utf-8", "gbk",daddslashes(trim($_GET['mail']))):daddslashes(trim($_GET['mail']));
$pass1=daddslashes(trim($_GET['pass1']));
$pass2=daddslashes(trim($_GET['pass2']));

if ($act=="check") {
    // �û���
    if(!$username) {
        echo "����д�����û���!";
        exit;
    }
    if(StrLenW($username)>16 || StrLenW($username)<4) {
        echo "�û�������Ӧ�ô���4С��16���ַ���";
        exit;
    }
    $sql = "select user_id from et_users where user_name='$username'";
	$query = $db->query($sql);
    if ($db->fetch_array($query)) {
        echo "�û����Ѵ��ڣ�����ʹ��!";
        exit;
    }
    // �����ʼ�
    if(!$mailadres) {
        echo "����д�����ʼ���ַ!";
        exit;
    }
    $t=explode("@",$mailadres);
    if(!$t[1]) {
        echo "�����ʼ���ʽ����ȷ��";
        exit;
    }
    $sql = "select user_id from et_users where mailadres='$mailadres'";
	$query = $db->query($sql);
    if ($db->fetch_array($query)) {
        echo "�˵����ʼ��Ѵ��ڣ�����ʹ��!";
        exit;
    }
    // ����
    if (StrLenW($pass1)<6 || StrLenW($pass1)>32) {
        echo "���볤�Ȳ���С��6λ!";
        exit;
    }
    if ($pass1!=$pass2) {
        echo "������������벻һ��!";
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
            echo "ע��ʧ�ܣ�������Ϊ��û����д����ע����Ϣ����������д��";
            exit;
        }
    } else {
        echo "ע��ʧ�ܣ�������Ϊ��û����д����ע����Ϣ����������д��";
        exit;
    }
}

//ģ���Foot
$web_name3="���û�ע��";
$sqlnum=$db->querynum;
$mtime = explode(' ', microtime());
$loadtime=$mtime[1] + $mtime[0] - $starttime;
include($template->getfile('op_register.htm'));
?>
