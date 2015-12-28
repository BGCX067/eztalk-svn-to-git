<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

$page=$_GET['page']?$_GET['page']:"1";
$sid=$_GET['sid']?$_GET['sid']:$_POST['sid'];

$sql="SELECT s.*,u.user_id,u.user_name,u.user_head,u.theme_bgcolor,u.theme_pictype,u.theme_text,u.theme_link,u.theme_sidebar,u.theme_sidebox,u.theme_bgurl FROM et_users AS u,et_share AS s where s.user_id=u.user_id && s.share_id='$sid'";
$query = $db->query($sql);
$data = $db->fetch_array($query);
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

$user=array("user_id"=>$data['user_id'],"theme_bgcolor"=>$data['theme_bgcolor'],"theme_pictype"=>$data['theme_pictype'],"theme_text"=>$data['theme_text'],"theme_link"=>$data['theme_link'],"theme_sidebar"=>$data['theme_sidebar'],"theme_sidebox"=>$data['theme_sidebox'],"theme_bgurl"=>$data['theme_bgurl']);

if (!$share_id) {
    header("location: $webaddr/op/share&tip=38");
    exit;
}

if ($action=="sendreply") {
    $content=daddslashes(trim($_POST['content']));
    if ($share_id && $content) {
        $ret=$retimes+1;
        $db->query("INSERT INTO et_sharereply (share_id,user_id,reply_body,reply_time) VALUES ('$share_id','$my[user_id]','$content','$addtime')");
        $db->query("UPDATE et_share  SET retimes='$ret' where share_id='$share_id'");

        fsock($share_uid,"【小T提醒】 ".$my[user_name]."回复了您的分享，查看地址:".$webaddr."/op/sharereply/".$share_id);
        header("location: $webaddr/op/sharereply/$share_id&tip=39");
    } else {
        header("location: $webaddr/op/sharereply/$share_id&tip=40");
    }
}

if ($act=="del") {
    $cid=$_GET['cid'];
    $t=$db->query("DELETE FROM et_sharereply where shre_id='$cid' && user_id='$my[user_id]'");
    if ($t==1) {
        $ret=$retimes-1;
        $db->query("UPDATE et_share  SET retimes='$ret' where share_id='$sid'");
        echo "success";
        exit;
    } else {
        echo "很抱歉，删除评论失败了，";
        exit;
    }
}

$i=0;
$start= ($page-1)*$home_num;
$sql="SELECT r.*,u.user_id,u.user_name,u.user_head FROM et_users AS u,et_sharereply AS r where r.user_id=u.user_id && r.share_id='$sid' order by r.shre_id desc limit $start,$home_num";
$query = $db->query($sql);
while($data = $db->fetch_array($query)) {
    $i=$i+1;
    $shre_id=$data['shre_id'];
    $reuid=$data['user_id'];
    $reuname=$data['user_name'];
    $reuhead=$data['user_head']?"$webaddr/attachments/head/".$data['user_head']:"$webaddr/images/noavatar.jpg";
    $reply_body=ubb($data['reply_body']);
    $reply_time=timeop($data['reply_time']);

    $reply[] = array("shre_id"=>$shre_id,"reuid"=>$reuid,"reuname"=>$reuname,"reuhead"=>$reuhead,"reply_body"=>$reply_body,"reply_time"=>$reply_time);
}

$query = $db->query("select count(*) as count from et_sharereply where share_id='$sid'");
$row = $db->fetch_array($query);
$total=$row['count'];
$pg_num=$total/$index_num;
$pg_num=intval($pg_num);
if ($total!=$pg_num*$index_num)
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
if ($retimes!=$total) {
    $db->query("UPDATE et_share  SET retimes='$ret' where share_id='$sid'");
}

//模板和Foot
$web_name3="分享".$typedc;
$sqlnum=$db->querynum;
$mtime = explode(' ', microtime());
$loadtime=$mtime[1] + $mtime[0] - $starttime;
include($template->getfile('op_sharereply.htm'));
?>