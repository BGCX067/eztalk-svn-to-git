<?php
error_reporting(7);
date_default_timezone_set("PRC");
define('IN_ET', TRUE);
include('../include/etfunctions.func.php');
define('WAP_ROOT', dirname(__FILE__));
define('ET_ROOT', get_substr(WAP_ROOT,0,-4));
include('../include/db_mysql.class.php');
include('../config.inc.php');
$db = new dbstuff;
$db->connect($server,$db_username,$db_password,$db_name, $pconnect,true,"GBK");
include('../include/cache.inc.php');
@mysql_query("set names gbk");
include('include/global.func.php');
include('include/chinese.class.php');
if(preg_match('/(mozilla|m3gate|winwap|openwave|Opera)/i', $_SERVER['HTTP_USER_AGENT']) && !preg_match('/(SymbianOS)/i', $_SERVER['HTTP_USER_AGENT'])) {
	header("Location: ../index.php");
}

$op=$_GET['op']?$_GET['op']:"index";
$addtime=time();
$action=$_POST['action'];
$act=$_GET['act'];
$page=$_GET['page']?$_GET['page']:1;

$exp=authcode($_COOKIE["wapcookie"],'DECODE');
$ulmtem=explode("\t",$exp);

if ($ulmtem) {
	$query = $db->query("SELECT user_id,user_name FROM et_users where user_id='$ulmtem[0]' && password='$ulmtem[1]'");
	$data = $db->fetch_array($query);
    $user_id=$data['user_id'];
	$user_name=$data['user_name'];
}

if (!$user_id) $head=$webn1." | 迷你博客 随时随地";
else $head=$webn1." | 欢迎您，".$user_name;

if (!$user_id && $op=="index") $op="login";

wapheader($head);

include('include/'.$op.'.inc.php');

if ($user_id && $op!="logout") {
    echo "<div id=\"nav\" style=\"border-top:1px solid #8FE1FF;margin-top:5px;padding-top:5px;padding-bottom:10px\">".
        "<a href='index.php?op=index'>首页</a> | ".
        "<a href='index.php?op=home'>空间</a> | ".
        "<a href='index.php?op=atreplies'>@我</a> | ".
        "<a href='index.php?op=myfriends'>动态</a> | ".
        "<a href='index.php?op=privatemsg'>私信</a><br/>".
        "<a href='index.php?op=friends'>关注</a> | ".
        "<a href='index.php?op=browse'>逛逛</a> | ".
        "<a href='index.php?op=sendphoto'>发照片</a> | ".
        "<a href='index.php?op=login&act=logout'>退出</a></div>";
}
wapfooter();
?>