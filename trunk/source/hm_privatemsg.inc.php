<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

if ($user[user_id]!=$my[user_id]) {
    header("Location: $webaddr/home");
}

if ($act=="delmsg") {
    tologin();
    $db->query("DELETE FROM et_messages WHERE (js_id ='$my[user_id]' || fs_id ='$my[user_id]') && message_id='".trim($_GET["mid"])."'");
    echo "success";
    exit;
}

$i=0;
$start= ($page-1)*$home_num;
if ($_GET['pm']=="my") {
    $sql = "select m.message_id,m.message_body,m.m_time,m.isread,u.user_id,u.user_name,u.user_head from et_messages AS m,et_users AS u where m.fs_id=u.user_id && m.js_id='$my[user_id]' order by m.message_id desc limit $start,$home_num";
} elseif($_GET['pm']=="send") {
    $sql = "select m.message_id,m.message_body,m.m_time,m.isread,u.user_id,u.user_name,u.user_head from et_messages AS m,et_users AS u where m.js_id=u.user_id && m.fs_id='$my[user_id]' order by m.message_id desc limit $start,$home_num";
}
$query = $db->query($sql);
while ($data=$db->fetch_array($query)) {
    $i=$i+1;
    $contentid=$data['message_id'];
    $home_uid=$data['user_id'];
    $home_uname=$data['user_name'];
    $home_uhead=$data['user_head']?"$webaddr/attachments/head/".$data['user_head']:"$webaddr/images/noavatar.jpg";
    $content=$data['message_body'];
    $content=ubb($content);
    $posttime=timeop($data['m_time']);
    $isread=$data['isread']?1:2;

    $mymsg[] = array("contentid"=>$contentid,"home_uid"=>$home_uid,"home_uname"=>$home_uname,"home_uhead"=>$home_uhead,
                    "content"=>$content,"posttime"=>$posttime,"isread"=>$isread);
}

$sql_f = "select u.user_id,u.user_name from et_friend as f,et_users as u where f.fid_fasong='$user[user_id]' && f.fid_jieshou=u.user_id order by f.fri_id desc";
$query_f = $db->query($sql_f);
while ($data=$db->fetch_array($query_f)){
    $usid=$data['user_id'];
    $usname=$data['user_name'];

    $friloop[] = array("usid"=>$usid,"usname"=>$usname);
}

if ($user[ly]!=0) {
    $db->query("UPDATE et_messages  SET isread = 1 where js_id='$my[user_id]'");
}

$query_ly = $db->query("select count(*) as count from et_messages where js_id='$user[user_id]' && isread='0'");
$row = $db->fetch_array($query_ly);
$noread = $row['count'];

if ($_GET['pm']=="my") {
    $sql ="select count(*) AS count from et_messages where js_id='$my[user_id]'";
} elseif($_GET['pm']=="send") {
    $sql ="select count(*) AS count from et_messages where fs_id='$my[user_id]'";
}
$query = $db->query($sql);
$row = $db->fetch_array($query);
$total=$row['count'];
$pg_num=$total/$home_num;
$pg_num=intval($pg_num);
if ($total!=$pg_num*$home_num)
    $pg_num=$pg_num+1;
$np=$page+1;
$pp=$page-1;
if ($pg_num>4) {
    if ($pg_num-$page<=3)
        $page_from=($page-1)==0?1:$pg_num-4;
    else
        $page_from=($page-1)==0?1:$page-1;
    $page_end=($page_from+4)>$pg_num?$pg_num:$page_from+4;
}else{
    $page_from=1;
    $page_end=$pg_num;
}

include($template->getfile('hm_privatemsg.htm'));
?>