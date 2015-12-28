<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

tologin();

//Ä£°åºÍFoot
$web_name3="²å¼þ";
$sqlnum=$db->querynum;
$mtime = explode(' ', microtime());
$loadtime=$mtime[1] + $mtime[0] - $starttime;
include($template->getfile('op_badge.htm'));
?>