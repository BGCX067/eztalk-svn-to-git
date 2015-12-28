<?php
include('common.inc.php');

if (!file_exists(ET_ROOT."/include/syst/lockstall.syst")) {
    header("Location: install/install.php");
    exit;
}

if ($_GET['uid']) {
    header("location: $webaddr/home/u.".$_GET['uid']);
    exit;
}

tohome();

//Ä£°åºÍFoot
$sqlnum=$db->querynum;
$mtime = explode(' ', microtime());
$loadtime=$mtime[1] + $mtime[0] - $starttime;
include($template->getfile('index.htm'));
?>