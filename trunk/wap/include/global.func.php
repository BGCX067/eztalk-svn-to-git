<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

function transsid($url, $tag = '', $wml = 0) {
    global $ulm;
	$tag = stripslashes($tag);
	if(!$tag || (!preg_match("/^(http:\/\/|mailto:|#|javascript)/i", $url) && !strpos($url, 'ulm='))) {
		if($pos = strpos($url, '#')) {
			$urlret = substr($url, $pos);
			$url = substr($url, 0, $pos);
		} else {
			$urlret = '';
		}
		$url .= (strpos($url, '?') ? ($wml ? '&amp;' : '&') : '?').'ulm='.$ulm.$urlret;
	}
	return $tag.$url;
}

function wapheader($title) {
	ob_start();
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>".
		"<!DOCTYPE html PUBLIC \"-//WAPFORUM//DTD XHTML Mobile 1.0//EN\" \"http://www.wapforum.org/DTD/xhtml-mobile10.dtd\">".
		"<html xmlns=\"http://www.w3.org/1999/xhtml\">".
		"<head>".
	    "<title> $title</title>".
		"<style type=\"text/css\">".
	    "body,ul,ol,form{margin:0 0;padding:0 0;font:normal 14px Arial, Helvetica;line-height:150%}".
	    "ul,ol{list-style:none}".
	    "h2,.s{border-bottom:1px solid #ccc}".
	    "h1,h2,h3,h6,div,li,p{margin:0 0;padding:2px 2px;font-size:14px}".
	    "h1{background:#8FE1FF}".
		"h2{background:#d2edf6}".
        "h6{background:#fffcaa;border-bottom:1px solid #ffed00}".
        "a {color:#06c; text-decoration:underline;}".
        "a:hover {text-decoration:none;}".
        ".page {margin-top:5px}".
        ".showmag {margin:10px 0 10px 0;font-size:14px}".
		".n{border:1px solid #ffed00;background:#fffcaa}".
		".t,.a,.stamp,.stamp a,#ft{color:#999;font-size:12px}".
        "img {border:0; vertical-align:middle;}".
		"</style>".
		"</head>".
		"<body>".
	    "<h1><center><a href=\"index.php\"><img src=\"include/logo.gif\" alt=\"EasyTalk\" /></a></center></h1>";
}

function wapfooter() {
    echo "<div id=\"ft\">ET报时：".gmdate('m-d H:i',time()+8*3600)."</div>".
		 "<div id=\"ft\">Powered by EasyTalk</div>".
		 "</body>".
		 "</html>";
	wmloutput();
}

function wmloutput() {
	global $charset, $wapcharset, $chs;
	//$content = preg_replace("/\<a(\s*[^\>]+\s*)href\=([\"|\']?)([^\"\'\s]+)/ies", "transsid('\\3','<a\\1href=\\2',1)", ob_get_contents());
    $content =ob_get_contents();
	ob_end_clean();
	if($charset != 'utf-8') {
		$target = $wapcharset == 1 ? 'UTF-8' : 'UNICODE';
		if(empty($chs)) {
			$chs = new Chinese($charset, $target);
		} else {
			$chs->config['SourceLang'] = $chs->_lang($charset);
			$chs->config['TargetLang'] = $target;
		}
		echo ($wapcharset == 1 ? $chs->Convert($content) : str_replace(array('&#x;', '&#x0;'), array('??', ''), $chs->Convert($content)));

	} else {
		echo $content;
	}
}


//wap 链接过滤
function urlreplace($content) {
    $cbody=preg_replace("/\[(quote|b|u|i|s|colour=.*|url|url=.*|img.*|strike|size=.*)\](\S+?)\[\/.*\]/i","$2",$content);
    $cbody =eregi_replace('(.+)我在相册上传了一张照片(.+)', '我在相册上传了一张照片\2', $cbody);
    if (!preg_match('/(wap\/index.php\?op=home&uid=)/i', $cbody)) {
        $cbody =eregi_replace('href=(.+)', 'href=http://gate.baidu.com/tc?bd_page_type=1&src=\1', $cbody);
        $cbody = str_replace("'", "", $cbody);
        $cbody = str_replace('"', "", $cbody);
    }
    return $cbody;
}
?>