<?php
include('common.inc.php');

$page=$_GET['page']?intval($_GET['page']):1;

//管理员删除
if ($act=="del" && $my[isadmin]==1) {
	$contentid=$_GET['sid'];
    $db->query("DELETE FROM et_content WHERE content_id='$contentid'");
	echo "success";
    exit;
}

//大家再说什么
$i=0;
$start= ($page-1)*$index_num;
$q=daddslashes($_GET['q']);
$type=$_GET['t']?$_GET['t']:"s1";
if ($act=="search") {
    if ($type=="s1")
        $query = $db->query("SELECT s.content_body,s.content_id,s.posttime,s.type,s.status_id,s.status_uname,s.status_type,u.user_id,u.user_name,u.user_head,t.topic_id,t.topic_body,t.open FROM et_users AS u,et_content AS s left join et_topic AS t on s.topicid=t.topic_id && t.open=1 where s.user_id=u.user_id && s.content_body like '%$q%'order by s.content_id desc limit $start,$index_num");
    elseif ($type=="s2")
        $query = $db->query("SELECT s.content_id,s.content_body,s.posttime,s.type,s.status_id,s.status_uname,s.status_type,u.user_id,u.user_name,u.user_head,t.topic_id,t.topic_body,t.open FROM et_users AS u,et_content AS s left join et_topic AS t on s.topicid=t.topic_id && t.open=1 where s.user_id=u.user_id && s.content_body like '%$q%' && s.user_id='$my[user_id]' order by s.content_id desc limit $start,$index_num");
} elseif (empty($act)) {
    $query = $db->query("SELECT s.content_id,s.content_body,s.posttime,s.type,s.status_id,s.status_uname,s.status_type,u.user_id,u.user_name,u.user_head,t.topic_id,t.topic_body,t.open FROM et_users AS u,et_content AS s left join et_topic AS t on s.topicid=t.topic_id && t.open=1 where s.user_id=u.user_id order by s.content_id desc limit $start,$index_num");
}
while($data = $db->fetch_array($query)) {
    $i=$i+1;
    $content_id=$data['content_id'];
    $content_uid=$data['user_id'];
    $content_uname=$data['user_name'];
    $content_uhead=$data['user_head']?"$webaddr/attachments/head/".$data['user_head']:"$webaddr/images/noavatar.jpg";
    $content_body=$data['content_body'];
    $content_body=ubb($content_body);
    $posttime=timeop($data['posttime']);
    $content_type=$data['type'];
    $statusid=$data['status_id'];
    $status_uname=$data['status_uname'];
    $status_type=$data['status_type'];

    $topic_body=$data['topic_body']?"<a href='$webaddr/op/topic/$data[topic_id]' style='text-decoration:none;'><font color='red'>[".$data['topic_body']."]</font></a>&nbsp;":"";

    $content[] = array("content_id"=>$content_id,"content_uid"=>$content_uid,"content_uname"=>$content_uname,"content_uhead"=>$content_uhead,
                 "content_body"=>$content_body,"posttime"=>$posttime,"content_type"=>$content_type,"statusid"=>$statusid,"status_uname"=>$status_uname
                 ,"status_type"=>$status_type,"topic_body"=>$topic_body);
}

//分页
if ($act=="search") {
    if ($type=="s1")
        $query = $db->query("select count(*) as count from et_content where content_body like '%$q%'");
    elseif ($type=="s2")
        $query = $db->query("select count(*) as count from et_content where content_body like '%$q%' && user_id='$user_id'");
} elseif (empty($act)) {
    $query = $db->query("select count(*) as count from et_content");
}
$row = $db->fetch_array($query);
$total=$row['count'];
$pg_num=$total/$index_num;
$pg_num=intval($pg_num);
if ($total!=$pg_num*$index_num)
    $pg_num=$pg_num+1;
$np=$page+1;
$pp=$page-1;

//模板和Foot
$web_name3="随便看看";
$sqlnum=$db->querynum;
$mtime = explode(' ', microtime());
$loadtime=$mtime[1] + $mtime[0] - $starttime;
include($template->getfile('browse.htm'));
?>