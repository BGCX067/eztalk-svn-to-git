<?php
header('Content-Type:text/html;charset=GB2312');
error_reporting(7);
define('IN_ET', TRUE);
define('ET_ROOT', dirname(__FILE__));
include(ET_ROOT."/include/db_mysql.class.php");
include('config.inc.php');

$db = new dbstuff;
$db->connect($server,$db_username,$db_password,$db_name, $pconnect,true,"GBK");
@mysql_query("set names GBK");

mysql_query("alter table et_users add musicaddr varchar(200) default NULL");
mysql_query("alter table et_users add qq int(15) default NULL");
mysql_query("alter table et_users add msn varchar(50) default NULL");
mysql_query("alter table et_users add gtalk varchar(50) default NULL");
mysql_query("alter table et_users add getmsgtype varchar(10) default NULL");

mysql_query("alter table et_album change face_photo face_photo varchar(30) default NULL");
mysql_query("alter table et_photos change pt_name pt_name varchar(30) not NULL");

echo "Éý¼¶Íê³É£¡";
?>