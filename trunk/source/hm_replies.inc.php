<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

//别人不能查看
if ($user[user_id]!=$my[user_id]) {
    header("Location: $webaddr/home");
}

$i=0;
$start= ($page-1)*$home_num;
$sql="SELECT s.content_id,s.content_body,s.posttime,s.type,s.status_id,s.status_type,u.user_id,u.user_name,u.user_head,t.topic_id,t.topic_body,t.open FROM et_users AS u,et_content AS s left join et_topic AS t on s.topicid=t.topic_id && t.open=1 where s.user_id=u.user_id && s.status_uid='$my[user_id]' order by s.content_id desc limit $start,$home_num";
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
    $status_type=$data['status_type'];
    $type=$data['type'];
    $topic_body=$data['topic_body']?"<a href='$webaddr/op/topic/$data[topic_id]' style='text-decoration:none;'><font color='red'>[".$data['topic_body']."]</font></a>&nbsp;":"";

    $replies[] = array("contentid"=>$contentid,"home_uid"=>$home_uid,"home_uname"=>$home_uname,"home_uhead"=>$home_uhead,"content"=>$content,"posttime"=>$posttime,"type"=>$type,"statusid"=>$statusid,"status_type"=>$status_type,"topic_body"=>$topic_body);
}

$sql ="select count(*) AS count from et_content where status_uid='$my[user_id]'";
$query = $db->query($sql);
$row = $db->fetch_array($query);
$total=$row['count'];
$pg_num=$total/$home_num;
$pg_num=intval($pg_num);
if ($total!=$pg_num*$home_num)
    $pg_num=$pg_num+1;
$np=$page+1;
$pp=$page-1;

include($template->getfile('hm_replies.htm'));
?>