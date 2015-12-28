<?php
header('Content-Type:text/html;charset=GB2312');

date_default_timezone_set("PRC");
error_reporting(7);
define('IN_ET', TRUE);

$addtime=time();
$action=$_POST['action'];
$act=$_GET['act'];
$step=$_GET['step']?$_GET['step']:1;
$step=$step>4?1:$step;

if(PHP_VERSION < '4.1.0') {
	$_GET = &$HTTP_GET_VARS;
	$_POST = &$HTTP_POST_VARS;
	$_COOKIE = &$HTTP_COOKIE_VARS;
	$_SERVER = &$HTTP_SERVER_VARS;
	$_ENV = &$HTTP_ENV_VARS;
	$_FILES = &$HTTP_POST_FILES;
}

if (file_exists("../include/syst/lockstall.syst")) {
    header("Location: ../index.php");
    exit;
}

$sqlfile=dirname(__FILE__)."/easytalk.sql";
$lockfile = '../include/syst/lockstall.syst';

include("../include/db_mysql.class.php");
include('../config.inc.php');
include("../include/template.class.php");
include('../include/etfunctions.func.php');
include('install.func.php');

$options = array(
    'template_dir' => './',
    'cache_dir' => './',
    'auto_update' => true,
    'cache_lifetime' => 0,
);
$template = Template::getInstance();
$template->setOptions($options);


$s1=dir_writeable("../include/cache");
$s2=dir_writeable("../include/syst");
$s3=dir_writeable("../attachments/head");
$s4=dir_writeable("../attachments/usertemplates");
$s5=dir_writeable("../templates/default/cache");
$s6=dir_writeable("../admin/backup");
$s7=dir_writeable("../admin/templates/cache");
$s8=dir_writeable("../attachments/photo");


if (($s1!=1 || $s2!=1 || $s3!=1 || $s4!=1 || $s5!=1 || $s6!=1 || $s7!=1 || $s8!=1) && $step!=1) {
    header("location: install.php?step=1");
    exit;
}

if ($act=="checkinstall") {
    $link = @mysql_connect($server, $db_username, $db_password, 1);
	$connnect = $link && @mysql_select_db($db_name, $link) ? 'yes' : 'no';
    if ($connnect=="yes" && $server && $db_username && $db_password && $db_name) {
        header("location: install.php?step=3");
        exit;
    } else {
        echo "<script>alert('数据库检测未通过，请重新修改 config.inc.php 文件！');location.href='install.php?step=2'</script>";
        exit;
    }
}

if ($action=="install") {
    $username=daddslashes(trim($_POST['username']));
    $mailadres=daddslashes(trim($_POST['mailadres']));
    $password1=md5(md5($_POST['password1']));
    $password2=md5(md5($_POST['password2']));
    if(StrLenW($username)>16 || StrLenW($username)<4) {
        echo "<script>alert('用户名长度应该大于4小于16个字符！');location.href='install.php?step=3'</script>";
        exit;
    }
    $t=explode("@",$mailadres);
    if(!$t[1]) {
        echo "<script>alert('电子邮件格式不正确！');location.href='install.php?step=3'</script>";
        exit;
    }
    if ($password1!=$password2) {
        echo "<script>alert('两次输入的密码不正确！');location.href='install.php?step=3'</script>";
        exit;
    }

    if ($password1==$password2 && $password1 && $password2) {
        $web_name3="EasyTalk 安装";
        include($template->getfile('install.htm'));
        $sql = file_get_contents($sqlfile);
        $db = new dbstuff;
        $db->connect($server,$db_username,$db_password,$db_name, $pconnect,true,"GBK");
        @mysql_query("set names GBK");
        runquery($sql);
        $db->query("INSERT INTO et_users  (user_name,password,mailadres,signupdate,isadmin) VALUES ('$username','$password2','$mailadres','$addtime','1')");
        showjsmessage("管理员账号写入成功！");
        @touch($lockfile);
        exit;
    } else {
        echo "<script>alert('密码输入不正确，请重新输入！');location.href='install.php?step=3'</script>";
        exit;
    }
}

//模板和Foot
$web_name3="EasyTalk 安装";
include($template->getfile('install.htm'));
?>