<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

openweb();

//ฤฃฐๅบอFoot
$web_name3="อ๘ีพนุฑี";
$sqlnum=$db->querynum;
$mtime = explode(' ', microtime());
$loadtime=$mtime[1] + $mtime[0] - $starttime;
include($template->getfile('op_webclose.htm'));
?>