<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

//机器人回复
function fsock($uid,$packet) {
    global $db,$serverip,$serverport,$openserver;
    if ($openserver==1) {
        $sql = "select msn,qq,gtalk,getmsgtype from et_users where user_id='$uid'";
        $query = $db->query($sql);
        $data = $db->fetch_array($query);
        $_getmsgtype=$data['getmsgtype'];

        $_qq=$data['qq'];
        $_gtalk=$data['gtalk'];

        if ($_getmsgtype=="msn") {
            $_msn=$data['msn'];
            $tem=explode(" ",$_msn);
            if (count($tem)==2) {
                $_msn=$tem[0];
                $_msnyz=$tem[1];
            } else $_msnyz="";
            $packet="im|et|$_msn|et|$packet";
            if (!$_msnyz && $_msn) {
                $fp = @fsockopen( $serverip , $serverport, $errNo , $errstr, 1);
                if( !$fp ) return false;
                else {
                    stream_set_timeout( $fp , 3 ) ;
                    fwrite( $fp , $packet ) ;
                    $status = stream_get_meta_data( $fp ) ;
                    if( $status['timed_out'] ) {
                        fclose( $fp );
                        return false;
                    }
                }
            }
        }
    }
}

//cookie处理
function dsetcookie($var, $value, $life = 0, $prefix = 1) {
	global $cookiedomain, $cookiepath, $addtime, $_SERVER;
	setcookie($var, $value,$life ? $addtime + $life : 0, $cookiepath,$cookiedomain, $_SERVER['SERVER_PORT'] == 443 ? 1 : 0);
}

function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    global $webaddr;
    $auth_key=md5($webaddr.$_SERVER['HTTP_USER_AGENT']);
	$ckey_length = 4;
	$key = md5($key ? $key : $auth_key);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}

//addslashes() 函数在指定的预定义字符前添加反斜杠
function daddslashes($string) {
    $string=str_replace("'",'"',$string);
    !defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
    if(!MAGIC_QUOTES_GPC) {
        if(is_array($string)) {
            foreach($string as $key => $val) {
                $string[$key] = daddslashes($val);
            }
        } else {
            $string = addslashes($string);
        }
    }
	return $string;
}

//随机数
function randStr($len=6) {
    $chars='ABDEFGHJKLMNPQRSTVWXYabdefghijkmnpqrstvwxy23456789#%*';
    mt_srand((double)microtime()*1000000*getmypid());
    $password='';
    while(strlen($password)<$len)
    $password.=substr($chars,(mt_rand()%strlen($chars)),1);
    return $password;
}

//返回时间
function timeop($time) {
    $ntime=time()-$time;
    if ($ntime<60) {
        return("刚才");
    } elseif ($ntime<3600) {
        return(intval($ntime/60)."分钟前");
    } else {
        return(gmdate('Y-m-d H:i',$time+8*3600));
    }
}

//数组用逗号隔开
function implodeids($array) {
	if(!empty($array)) {
		return "'".implode("','", is_array($array) ? $array : array($array))."'";
	} else {
		return '';
	}
}

function StrLenW($str){
    $len=strlen($str);
    $i=0;
    while($i<$len){
        if(preg_match("/^[".chr(0xa1)."-".chr(0xff)."]+$/",$str[$i])){
            $i+=2;
        }else{
            $i+=1;
        }
    }
    return $i;
}

function get_substr($string,$start='0',$length='') {
    $start = (int)$start;
    $length = (int)$length;
    $i = 0;
    if(!$string) {
        return;
    }
    if($start>=0) {
        while($i<$start) {
            if(ord($string[$i])>127) {
                $i = $i+2;
            } else {
                $i++;
            }
        }
        $start = $i;
        if($length=='') {
            return substr($string,$start);
        }
        elseif($length>0) {
            $end = $start+$length;
            while($i<$end) {
                if(ord($string[$i])>127) {
                    $i = $i+2;
                } else {
                    $i++;
                }
            } if($end != $i-1) {
                $end = $i;
            } else {
                $end--;
            }
            $length = $end-$start;
            return substr($string,$start,$length);
        } elseif($length==0) {
            return;
        } else {
            $length = strlen($string)-abs($length)-$start;
            return get_substr($string,$start,$length);
        }
    } else {
        $start = strlen($string)-abs($start);
        return get_substr($string,$start,$length);
    }
}

//UBB代码支持
function ubb ($text) {
    global $webaddr;
    $dataset=array(0,1,2,3,4,5,6,7,8,9);
    $did=implode('',array_rand($dataset,9));

    $p    =    array(
                "/\[quote\](\S+?)\[\/quote\]/i",
                "/\[b\](\S+?)\[\/b\]/i",
                "/\[u\](\S+?)\[\/u\]/i",
                "/\[i\](\S+?)\[\/i\]/i",
                "/\[colour\=(.+?)\](.+?)\[\/colour\]/i",
                "/\[url\](.+?)\[\/url\]/i",
                "/\[url=(.+?)\](.+?)\[\/url\]/i",
                "/\[s\](.+?)\[\/s\]?/i",
                "/\[img\](.*?)\[\/img\]/i",
                "/\[img link=(.*?)\](.*?)\[\/img\]/i",
                "/\[strike\](.*?)\[\/strike\]/i",
                "/\[size=small\](.*?)\[\/size\]/i"
                );

    $r    =    array(
                "<font color=\"#757575\"><b><u>Quote: \"$1\"</b></u></font>",
                "<b>$1</b>",
                "<u>$1</u>",
                "<i>$1</i>",
                "<font color=\"$1\">$2</font>",
                "<a href=\"$1\" title=\"外部链接\" target=\"_blank\">$1</a>",
                "<a href=\"$1\" title=\"外部链接\" target=\"_blank\">$2</a>",
                "<s>$1</s>",
                "<a href=\"$1\" class=\"photo\" target=\"_blank\"><img src=\"$1\" height=\"50px\" border=\"0\" alt=\"EasyTalk\"></a>",
                "<a href=\"$1\" class=\"photo\"><img src=\"$2\" onerror=\"src='$webaddr/images/nophoto.jpg'\" height=\"50px\" border=\"0\" alt=\"EasyTalk\"></a>", //相册图片链接
                "<strike>$1</strike>",
                "<font size=\"1\">$1</font>"
                );

    return preg_replace($p, $r, $text);
}


//用户没有登录 跳转首页
function toindex() {
    global $my,$webaddr;
    if (!$my[user_id]) {
        header("Location: $webaddr/index");
        exit;
    }
}

//用户没有登录 跳转首页
function tologin() {
    global $my,$webaddr;
    if (!$my[user_id]) {
        dsetcookie('urlrefer', 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        header("Location: $webaddr/op/login&tip=36");
        exit;
    }
}

//跳转关闭
function toclose() {
    global $webaddr;
    if (!file_exists(ET_ROOT."/include/syst/isclose.syst")) {
        header("Location: $webaddr/op/webclose");
        exit;
    }
}

//开启网站
function openweb() {
    global $webaddr;
    if (file_exists(ET_ROOT."/include/syst/isclose.syst")) {
        header("Location: $webaddr/index");
        exit;
    }
}

//好友数目更新
function frinum($uid) {
    global $db;
    $sql ="select count(*) as count from et_friend as f1,et_friend as f2 where f1.fid_jieshou=f2.fid_fasong && f2.fid_jieshou=f1.fid_fasong && f1.fid_jieshou='$uid'";
    $query = $db->query($sql);
    $row = $db->fetch_array($query);
    $total=$row['count'];

    $db->query("update et_users set friend_num='$total' where user_id='$uid'");
}

//信息转换
function idtoname($uid) {
    global $db;
    $sql = "select user_name from et_users where user_id='$uid'";
    $query = $db->query($sql);
    $row= $db->fetch_array($query);
    $name =$row['user_name'];
    return $name;
}


//发邮件
function sendmail($to,$title,$send) {
    global $smtp,$smtpserver,$smtpserverport,$smtpuser,$smtppass,$smtpusermail;
    $smtp = new smtp($smtpserver, $smtpserverport, true, $smtpuser, $smtppass);
    $smtp->debug = true;
    $smtp->sendmail($to,$smtpusermail,$title, $send, "text");
}


function nametoid($uname) {
    global $db;
    $sql = "select user_id from et_users where user_name='$uname'";
    $query = $db->query($sql);
    $row= $db->fetch_array($query);
    $uid =$row['user_id'];
    return $uid;
}

function mailtoid($mail) {
    global $db;
    $sql = "select user_id from et_users where mailadres='$mail'";
    $query = $db->query($sql);
    $row= $db->fetch_array($query);
    $uid =$row['user_id'];
    return $uid;
}

function msntoid($msn) {
    global $db;
    $sql = "select user_id from et_users where msn='$msn'";
    $query = $db->query($sql);
    $row= $db->fetch_array($query);
    $uid =$row['user_id'];
    return $uid;
}

function isfriend($fid1,$fid2) {
    global $db;
    $f1=$f2=0;
    $allfriend=0;
    $query2 = $db->query("select fid_jieshou,fid_fasong from et_friend where fid_jieshou='$fid1' || fid_fasong='$fid1'");
    while ($data2 = $db->fetch_array($query2)) {
        $fid_jieshou=$data2['fid_jieshou'];
        $fid_fasong=$data2['fid_fasong'];
        if ($fid_jieshou==$fid1 && $fid_fasong==$fid2) $f1=1;
        if ($fid_jieshou==$fid2 && $fid_fasong==$fid1) $f2=1;
    }
    if ($f1==1 && $f2==1) $allfriend=1;
    $isallfriend=array("allfri"=>$allfriend,"fri"=>$f1,"fol"=>$f2);
    return $isallfriend;
}

function idtohead($uid) {
    global $db,$webaddr;
    $sql = "select user_head from et_users where user_id='$uid'";
    $query = $db->query($sql);
    $row= $db->fetch_array($query);
    $user_head =$row['user_head'];
    $user_head=$user_head?"$webaddr/attachments/head/".$user_head:"$webaddr/images/noavatar.jpg";
    return $user_head;
}

//用户登录跳转空间
function tohome() {
    global $my,$webaddr;
    if ($my[user_id]) {
        header("Location: $webaddr/home");
    }
}

//词语过滤
function replace($content){
    global $replace;
    $content=clean_html($content);
    if($content=="") {
        $content="<FONT COLOR=\"red\">***HTML代码已经过滤***</font>";
    }
    $bad = explode("|",$replace);
    @reset($bad);
    for ($i=0;$i<count($bad);$i++)
	$content= str_replace($bad[$i],"<FONT COLOR=\"red\">***</font>",$content);
    return $content;
}

//过滤html
function clean_html($html)
{
    $html =eregi_replace('<("|\')?([^ "\']*)("|\')?.*>([^<]*)<([^<]*)>', '\4', $html);
    $html = preg_replace('#</?.*?\>(.*?)</?.*?\>#i','',$html);
    $html = preg_replace('#(.*?)\[(.*?)\](.*?)javascript(.*?);(.*?)\[/(.*?)\](.*?)#','', $html);
    $html = preg_replace('#javascript(.*?)\;#','', $html);
    $html = str_replace("<br />", "", $html);
    $html = htmlspecialchars($html);
    return($html);
}


//链接过滤
function apiurlreplace($content) {
    $cbody=preg_replace("/\[(quote|b|u|i|s|colour=.*|url|url=.*|img.*|strike|size=.*)\](\S+?)\[\/.*\]/i","$2",$content);
    $cbody =eregi_replace('(.+)我在相册上传了一张照片(.+)', '我在相册上传了一张照片\2', $cbody);
    return $cbody;
}

function sizecount($filesize) {
	if($filesize >= 1073741824) {
		$filesize = round($filesize / 1073741824 * 100) / 100 . ' GB';
	} elseif($filesize >= 1048576) {
		$filesize = round($filesize / 1048576 * 100) / 100 . ' MB';
	} elseif($filesize >= 1024) {
		$filesize = round($filesize / 1024 * 100) / 100 . ' KB';
	} else {
		$filesize = $filesize . ' Bytes';
	}
	return $filesize;
}

function getFlash($link, $host) {
	$return = '';
	if('youku.com' == $host) {
		// http://v.youku.com/v_show/id_XNDg1MjA0ODg=.html
		preg_match_all("/id\_(\w+)\=/", $link, $matches);
		if(!empty($matches[1][0])) {
			$return = $matches[1][0];
		}
	} elseif('ku6.com' == $host) {
		// http://v.ku6.com/show/bjbJKPEex097wVtC.html
		preg_match_all("/\/([\w\-]+)\.html/", $link, $matches);
		if(1 > preg_match("/\/index_([\w\-]+)\.html/", $link) && !empty($matches[1][0])) {
			$return = $matches[1][0];
		}
	} elseif('youtube.com' == $host) {
		// http://tw.youtube.com/watch?v=hwHhRcRDAN0
		preg_match_all("/v\=([\w\-]+)/", $link, $matches);
		if(!empty($matches[1][0])) {
			$return = $matches[1][0];
		}
	} elseif('5show.com' == $host) {
		// http://www.5show.com/show/show/160944.shtml
		preg_match_all("/\/(\d+)\.shtml/", $link, $matches);
		if(!empty($matches[1][0])) {
			$return = $matches[1][0];
		}
	} elseif('mofile.com' == $host) {
		// http://tv.mofile.com/PPU3NTYW/
		preg_match_all("/\/(\w+)\/*$/", $link, $matches);
		if(!empty($matches[1][0])) {
			$return = $matches[1][0];
		}
	} elseif('sina.com.cn' == $host) {
		// http://you.video.sina.com.cn/b/16776316-1338697621.html
		preg_match_all("/\/(\d+)-(\d+)\.html/", $link, $matches);
		if(!empty($matches[1][0])) {
			$return = $matches[1][0];
		}
	} elseif('sohu.com' == $host) {
		// http://v.blog.sohu.com/u/vw/1785928
		preg_match_all("/\/(\d+)\/*$/", $link, $matches);
		if(!empty($matches[1][0])) {
			$return = $matches[1][0];
		}
	}
	return $return;
}

//截取链接
function sub_url($url, $length) {
	if(strlen($url) > $length) {
		$url = str_replace(array('%3A', '%2F'), array(':', '/'), rawurlencode($url));
		$url = substr($url, 0, intval($length * 0.5)).' ... '.substr($url, - intval($length * 0.3));
	}
	return $url;
}
?>