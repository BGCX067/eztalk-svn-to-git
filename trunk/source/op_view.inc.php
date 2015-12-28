<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

$viewid=$_GET['id'];

$sql="SELECT s.content_id,s.content_body,s.posttime,s.type,s.status_id,s.status_uname,s.status_type,u.user_id,u.user_name,u.user_head,u.theme_bgcolor,u.theme_pictype,u.theme_text,u.theme_link,u.theme_sidebar,u.theme_sidebox,u.theme_bgurl,t.topic_id,t.topic_body,t.open FROM et_users AS u,et_content AS s left join et_topic AS t on s.topicid=t.topic_id && t.open=1 where s.user_id=u.user_id && s.content_id='$viewid'";
$query = $db->query($sql);
$data = $db->fetch_array($query);
$content_id=$data['content_id'];
$content_uid=$data['user_id'];
$content_uname=$data['user_name'];
$content_uhead=$data['user_head']?"$webaddr/attachments/head/".$data['user_head']:"$webaddr/images/noavatar.jpg";
$content_body=ubb($data['content_body']);
$posttime=$data['posttime'];
$type=$data['type'];
$statusid=$data['status_id'];
$status_uname=$data['status_uname'];
$status_type=$data['status_type'];
$topic_body=$data['topic_body']?"<a href='$webaddr/op/topic&tid=$data[topic_id]' style='text-decoration:none;'><font color='red'>[".$data['topic_body']."]</font></a>&nbsp;":"";
$user=array("user_id"=>$data['user_id'],"theme_bgcolor"=>$data['theme_bgcolor'],"theme_pictype"=>$data['theme_pictype'],"theme_text"=>$data['theme_text'],"theme_link"=>$data['theme_link'],"theme_sidebar"=>$data['theme_sidebar'],"theme_sidebox"=>$data['theme_sidebox'],"theme_bgurl"=>$data['theme_bgurl']);

//不存在
if (!$content_uid) {
	echo "<script>alert(\"此TALK不存在，点击确定返回主页！\");window.location.href='$webaddr/home';</script>";
	exit;
}

$wn2=clean_html(preg_replace("/\[(quote|b|u|i|s|colour=.*|url|url=.*|img|strike|size=.*)\](\S+?)\[\/(quote|b|u|i|s|colour|url|img|strike|size)\]/i","$2",$data['content_body']));
$wn=get_substr($wn2,0,50);
if ($wn!=$wn2) {
	$wn=$wn."...";
}

//模板和Foot
$web_name3=$content_uname."：".$wn;
$sqlnum=$db->querynum;
$mtime = explode(' ', microtime());
$loadtime=$mtime[1] + $mtime[0] - $starttime;
include($template->getfile('op_view.htm'));
?>