<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

//会员删除日志
if ($act=="del") {
    tologin();
    $contentid=trim($_GET['cid']);
    $result=$db->query("DELETE FROM et_content WHERE content_id='$contentid' && user_id='$my[user_id]'");
    if ($result==1) {
        $db->query("UPDATE et_users SET msg_num='".($my[msg_num]-1)."' where user_id='$my[user_id]'");
        echo "success";
        exit;
    } else {
        echo "信息删除失败，可能网络错误或者您没有删除的权限！";
        exit;
    }
}

$i=0;
$start= ($page-1)*$home_num;
$sql="SELECT s.content_id,s.content_body,s.posttime,s.type,s.status_id,s.status_uname,s.status_type,u.user_id,u.user_name,u.user_head,t.topic_id,t.topic_body,t.open FROM et_users AS u,et_content AS s left join et_topic AS t on s.topicid=t.topic_id && t.open=1 where s.user_id=u.user_id && s.user_id='$user[user_id]' order by s.content_id desc limit $start,$home_num";
$query = $db->query($sql);
while($data = $db->fetch_array($query)) {
    $i=$i+1;
    $contentid=$data['content_id'];
    $home_uid=$data['user_id'];
    $home_uname=$data['user_name'];
    $home_uhead=$data['user_head']?"$webaddr/attachments/head/".$data['user_head']:"$webaddr/images/noavatar.jpg";
    $content=$data['content_body'];
    $content=ubb($content);
    $posttime=timeop($data['posttime']);
    $statusid=$data['status_id'];
    $status_uname=$data['status_uname'];
    $status_type=$data['status_type'];
    $type=$data['type'];
    $topic_body=$data['topic_body']?"<a href='$webaddr/op/topic/$data[topic_id]' style='text-decoration:none;'><font color='red'>[".$data['topic_body']."]</font></a>&nbsp;":"";

    $home[] = array("contentid"=>$contentid,"home_uid"=>$home_uid,"home_uname"=>$home_uname,"home_uhead"=>$home_uhead,"content"=>$content,"posttime"=>$posttime,"type"=>$type,"statusid"=>$statusid,"status_uname"=>$status_uname,"status_type"=>$status_type,"topic_body"=>$topic_body);
}

$sql ="select count(*) AS count from et_content where user_id='$user[user_id]'";
$query = $db->query($sql);
$row = $db->fetch_array($query);
$total=$row['count'];
$pg_num=$total/$home_num;
$pg_num=intval($pg_num);
if ($total!=$pg_num*$home_num)
    $pg_num=$pg_num+1;
$np=$page+1;
$pp=$page-1;
if($my[msg_num]!=$total && $user[user_id]==$my[user_id]) {
    $db->query("update et_users set msg_num='$total' where user_id='$my[user_id]'");
}

include($template->getfile('hm_home.htm'));
?>