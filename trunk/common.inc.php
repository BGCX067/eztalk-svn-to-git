<?PHP
header('Content-Type:text/html;charset=GB2312');
date_default_timezone_set("PRC");
define('ET_ROOT', dirname(__FILE__));
define('IN_ET', TRUE);
error_reporting(7);
$mtime = explode(' ', microtime());
$starttime = $mtime[1] + $mtime[0];

$prev=$_SERVER['HTTP_REFERER'];
$addtime=time();
$action=$_POST['action'];
$act=$_GET['act'];
$refer=$_POST['refer']?$_POST['refer']:$_GET['refer']; //返回地址
$backto=$_POST['backto']?$_POST['backto']:$_GET['backto']; //ajax 回调

if(PHP_VERSION < '4.1.0') {
	$_GET = &$HTTP_GET_VARS;
	$_POST = &$HTTP_POST_VARS;
	$_COOKIE = &$HTTP_COOKIE_VARS;
	$_SERVER = &$HTTP_SERVER_VARS;
	$_ENV = &$HTTP_ENV_VARS;
	$_FILES = &$HTTP_POST_FILES;
}

include(ET_ROOT."/include/db_mysql.class.php");
include(ET_ROOT.'/config.inc.php');

$db = new dbstuff;
$db->connect($server,$db_username,$db_password,$db_name, $pconnect,true,"GBK");
@mysql_query("set names GBK");

include(ET_ROOT."/include/template.class.php");
include(ET_ROOT.'/include/etfunctions.func.php');
include(ET_ROOT.'/include/tip.lang.php');
include(ET_ROOT.'/include/cache.inc.php');

if (!$API) {
    if (is_admin_path!="yes" && $op!="webclose" && $op!="login") toclose();
    //模板开始
    if (is_admin_path=="yes") {                         //后台模板申明
        $options = array(
            'template_dir' => '../admin/templates',
            'cache_dir' => '../admin/templates/cache',
            'auto_update' => true,                      //当模板文件有改动时重新生成缓存 [关闭该项会快一些]
            'cache_lifetime' => 0,                      //缓存生命周期(分钟)，为 0 表示永久 [设置为 0 会快一些]
        );
    } else {
        $options = array(
            'template_dir' => 'templates/'.$temp_dir,
            'cache_dir' => 'templates/'.$temp_dir.'/cache',
            'auto_update' => true,
            'cache_lifetime' => 0,
        );
    }
    $template = Template::getInstance();
    $template->setOptions($options);
}

//信息调用
//后台
$admin_login_temp= $_COOKIE["admin_login"];
$admin_exp=authcode($admin_login_temp,'DECODE');
$admin_tem=explode("\t",$admin_exp);
$admin_login=$admin_tem['1'];
//前台
$authcookie = $_COOKIE["authcookie"];
$exp=authcode($authcookie,'DECODE');
$tem=explode("\t",$exp);
if ($tem || $admin_tem) {
    $sql_us = "select * from et_users where mailadres='$tem[1]' && password='$tem[0]' && user_id='$tem[2]'";
    $query_us = $db->query($sql_us);
    $my=$db->fetch_array($query_us);
    $my['user_head']=$my['user_head']?"$webaddr/attachments/head/".$my['user_head']:"$webaddr/images/noavatar.jpg";
    $tem1=explode(" ",$my['home_city']);
    $my['home_sf']=$tem1[0];
    $my['home_city']=$tem1[1];
    $tem2=explode(" ",$my['live_city']);
    $my['live_sf']=$tem2[0];
    $my['live_city']=$tem2[1];
    $tem3=explode("-",$my['birthday']);
    $my['birth_year']=$tem3[0];
    $my['birth_month']=$tem3[1];
    $my['birth_day']=$tem3[2];
    $tem4=explode(" ",$my['msn']);
    if (count($tem4)==2) {
        $my['msn']=$tem4[0];
        $my['msnyz']=$tem4[1];
    } else $my['msnyz']="";

    if ($my[user_id] && $addtime-$my[last_login]>$lastlogin) { //最后登陆时间
        $db->query("UPDATE et_users  SET last_login = '$addtime' where user_id='$my[user_id]'");
    }
}

//css文件
if (!file_exists(ET_ROOT.'/templates/'.$temp_dir.'/cache/style.css') && is_admin_path!="yes") {
    ob_start();
    include($template->getfile('style.css.htm'));
    $cssfile = ob_get_contents();
    $cssfile = preg_replace("/([\r\n])/", '', $cssfile);
    ob_end_clean();
    $fp = fopen(ET_ROOT."/templates/".$temp_dir."/cache/style.css",'w');
    fwrite($fp,$cssfile) or die('CSS文件写入失败！');
    fclose($fp);
}
?>