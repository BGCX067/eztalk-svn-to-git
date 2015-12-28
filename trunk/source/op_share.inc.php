<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

$uid=$_GET['uid']?$_GET['uid']:"all";
$page=$_GET['page']?$_GET['page']:"1";
$tp=$_GET['type']?$_GET['type']:"all";

if ($uid!="all") {
    $sql = "select user_name,theme_bgcolor,theme_pictype,theme_text,theme_link,theme_sidebar,theme_sidebox,theme_bgurl from et_users where user_id='$uid'";
    $query = $db->query($sql);
    $data= $db->fetch_array($query);
    $sharename=$data['user_name'];
    $user=array("user_id"=>$uid,"theme_bgcolor"=>$data['theme_bgcolor'],"theme_pictype"=>$data['theme_pictype'],"theme_text"=>$data['theme_text'],"theme_link"=>$data['theme_link'],"theme_sidebar"=>$data['theme_sidebar'],"theme_sidebox"=>$data['theme_sidebox'],"theme_bgurl"=>$data['theme_bgurl']);
    if (!$sharename) {
        echo "<script>alert('很抱歉，没有找到您要访问的用户！');location.href='$webaddr/op/share/u.$my[user_id]'</script>";
        exit;
    }
}

//删除
if ($act=="delshare") {
    tologin();
    $sid=$_GET['sid'];
    $result=$db->query("DELETE FROM et_share WHERE share_id='$sid' && user_id='$my[user_id]'");
    if ($result==1) {
        $db->query("UPDATE et_users SET share_num='".($my[share_num]-1)."' where user_id='$my[user_id]'");
        echo "success";
        exit;
    } else {
        echo "分享删除失败，可能网络错误或者您没有删除的权限！";
        exit;
    }
}

//分享
if ($action=="share") {
    $linkdata = array();
    $link = htmlspecialchars(trim($_POST['link']));

    $describe=clean_html($_POST['describe']);
    if (!preg_match("/^http\:\/\/.{4,300}$/i", $link) || !$link) {
        header("Location: $webaddr/op/share&tip=31");
        exit;
    } elseif (StrLenW($describe)>250) {
        header("Location: $webaddr/op/share&tip=32");
        exit;
    } else {
        // 判断是否视频
        $parseLink = parse_url($link);
        if(preg_match("/(youku.com|youtube.com|5show.com|ku6.com|sohu.com|mofile.com|sina.com.cn)$/i", $parseLink['host'], $hosts) && !preg_match("/\.swf$/i", $link)) {
            $flashvar = getFlash($link, $hosts[1]);
            if(!empty($flashvar)) {
                $type = 'video';
                $linkdata['flashvar'] = $flashvar;
                $linkdata['host'] = $hosts[1];
            }
        }
        // 判断是否音乐 mp3、wma
        else if(preg_match("/\.(mp3|wma)$/i", $link)) {
            $linkdata['musicvar'] = $link;
            $type = 'music';
        }
        // 判断是否 Flash
        else if(preg_match("/\.swf$/i", $link)) {
            $linkdata['flashaddr'] = $link;
            $type = 'flash';
        }
        else if(preg_match("/\.(jpg|gif|png|bmp)$/i", $link)) {
            $linkdata['pictureaddr'] = $link;
            $type = 'picture';
        }
        //判断网址
        else {
            $linkdata['website'] = $link;
            $type = 'website';
        }
        $linkdt=base64_encode(serialize($linkdata));
        if ($type) {
            $db->query("INSERT INTO et_share (user_id,link_data,content,sharetime,type) VALUES ('$my[user_id]','$linkdt','$describe','$addtime','$type')");

            if ($type=="video") $typedc="视频";
            else if ($type=="music") $typedc="音乐";
            else if ($type=="flash") $typedc="Flash";
            else if ($type=="website") $typedc="网址";
            else if ($type=="picture") $typedc="图片";

            if ($describe) $content=daddslashes("我分享了 一个".$typedc."，“".$describe."”，<a href='$webaddr/op/sharereply/".mysql_insert_id()."'>快去看看吧！</a>");
            else $content=daddslashes("我分享了 一个".$typedc."，<a href='$webaddr/op/sharereply/".mysql_insert_id()."'>快去看看吧！</a>");

            $db->query("UPDATE et_users SET share_num='".($my[share_num]+1)."' where user_id='$my[user_id]'");
            $db->query("INSERT INTO et_content (user_id,content_body,posttime) VALUES ('$my[user_id]','$content','$addtime')");

            header("Location: $webaddr/op/share&tip=33");
            exit;
        } else {
            header("Location: $webaddr/op/share&tip=37");
            exit;
        }
    }
}

if ($uid!="all") $ad="&& u.user_id='$uid'";
else $ad="";

$i=0;
$start= ($page-1)*$home_num;
if ($tp=="all")
    $sql="SELECT s.*,u.user_id,u.user_name,u.user_head FROM et_users AS u,et_share AS s where s.user_id=u.user_id && s.type!='' $ad order by s.share_id desc limit $start,$home_num";
else
    $sql="SELECT s.*,u.user_id,u.user_name,u.user_head FROM et_users AS u,et_share AS s where s.user_id=u.user_id && s.type='$tp' && s.type!='' $ad order by s.share_id desc limit $start,$home_num";
$query = $db->query($sql);
while($data = $db->fetch_array($query)) {
    $i=$i+1;
    $share_id=$data['share_id'];
    $share_uid=$data['user_id'];
    $share_uname=$data['user_name'];
    $share_uhead=$data['user_head']?"$webaddr/attachments/head/".$data['user_head']:"$webaddr/images/noavatar.jpg";
    $link_data=unserialize(base64_decode($data['link_data']));
    $content=$data['content'];
    $sharetime=timeop($data['sharetime']);
    $type=$data['type'];
    $retimes=$data['retimes'];
    if ($type=="video") $typedc="视频";
    else if ($type=="music") $typedc="音乐";
    else if ($type=="flash") $typedc="Flash";
    else if ($type=="website") $typedc="网址";
    else if ($type=="picture") $typedc="图片";

    $share[] = array("share_id"=>$share_id,"share_uid"=>$share_uid,"share_uname"=>$share_uname,"share_uhead"=>$share_uhead,"link_data"=>$link_data,"content"=>$content,"sharetime"=>$sharetime,"type"=>$type,"typedc"=>$typedc,"retimes"=>$retimes);
}

if ($uid=="all") {
    if ($tp=="all") $query = $db->query("select count(*) as count from et_share where type!=''");
    else $query = $db->query("select count(*) as count from et_share where type='$tp'");
} else {
    if ($tp=="all") $query = $db->query("select count(*) as count from et_share where user_id='$uid' && type!=''");
    else $query = $db->query("select count(*) as count from et_share where user_id='$uid' && type='$tp'");
}
$row = $db->fetch_array($query);
$total=$row['count'];
$pg_num=$total/$home_num;
$pg_num=intval($pg_num);
if ($total!=$pg_num*$home_num)
    $pg_num=$pg_num+1;
$np=$page+1;
$pp=$page-1;
if ($pg_num>8) {
    if ($pg_num-$page<=6)
        $page_from=($page-1)==0?1:$pg_num-7;
    else
        $page_from=($page-1)==0?1:$page-1;
    $page_end=($page_from+7)>$pg_num?$pg_num:$page_from+7;
}else{
    $page_from=1;
    $page_end=$pg_num;
}
if($my[share_num]!=$total && $uid==$my[user_id] && $uid!="all" && $tp=="all") {
    $db->query("update et_users set share_num='$total' where user_id='$my[user_id]'");
}

//模板和Foot
$web_name3="分享";
$sqlnum=$db->querynum;
$mtime = explode(' ', microtime());
$loadtime=$mtime[1] + $mtime[0] - $starttime;
include($template->getfile('op_share.htm'));
?>