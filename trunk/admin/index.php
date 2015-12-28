<?php
define('is_admin_path', 'yes');
include('../common.inc.php');

if ($admin_login !="yes" || $my[isadmin]!=1)
    header("Location: admin_login.php");

//服务器环境
$serverinfo = PHP_OS.' / PHP v'.PHP_VERSION;
$serverinfo .= @ini_get('safe_mode') ? ' Safe Mode' : NULL;
$dbversion = $db->result_first("SELECT VERSION()");
$dbsize = 0;
$query = $db->query("SHOW TABLE STATUS LIKE '$DBprefix%'", 'SILENT');
while($table = $db->fetch_array($query)) {
	$dbsize += $table['Data_length'] + $table['Index_length'];
}
$dbsize = $dbsize ? sizecount($dbsize) : "未知大小";

$filename1 = ET_ROOT.'/include/syst/isreg.syst';
$filename2 = ET_ROOT.'/include/syst/isclose.syst';

if ($action=="edit") {
    $web_name = daddslashes($_POST["web_name"]);
    $web_name2 = daddslashes($_POST["web_name2"]);
    $web_miibeian = daddslashes($_POST["web_miibeian"]);
    $seokey = daddslashes($_POST["seokey"]);
    $description = daddslashes($_POST["description"]);
    $R1 = $_POST["R1"];
    $R2 = $_POST["R2"];

    if ($R1==V4 && !file_exists($filename1))  @touch($filename1);
    else if ($R1==V3 && file_exists($filename1)) @unlink($filename1);

    if ($R2==V4 && !file_exists($filename2))  @touch($filename2);
    else if ($R2==V3 && file_exists($filename2)) @unlink($filename2);

    @unlink(ET_ROOT."/include/cache/setting.cache.php");

    $db->query("UPDATE et_settings  SET web_name ='$web_name',web_name2='$web_name2',web_miibeian='$web_miibeian',seokey='$seokey',description='$description'");

    header("Location: index.php");
}

include($template->getfile('index.htm'));
?>