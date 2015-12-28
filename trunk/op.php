<?php
include('common.inc.php');

$op = $_GET['op']?$_GET['op']:'view';

$allowviewarray=array("view","login","register","reset","faq");
if(!in_array($op, $allowviewarray)) tologin();

require('source/op_'.$op.'.inc.php');
?>