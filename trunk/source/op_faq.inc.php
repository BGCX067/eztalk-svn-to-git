<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

//ģ���Foot
$web_name3="���ְ���";
$sqlnum=$db->querynum;
$mtime = explode(' ', microtime());
$loadtime=$mtime[1] + $mtime[0] - $starttime;
include($template->getfile('op_faq.htm'));
?>