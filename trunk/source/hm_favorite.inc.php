<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

if ($act=="addfav") {
    tologin();
    $favid=$_GET['fid'];
    $sql = "select fav_id from et_favorite where content_id='$favid'";
	$query = $db->query($sql);
	$data = $db->fetch_array($query);
	if (!$data['fav_id']) {
        $sql = "select content_id from et_content where content_id='$favid'";
        $query = $db->query($sql);
        $data = $db->fetch_array($query);
        $content_id=$data['content_id'];
        if ($content_id) {
            $fnum=$my[fav_num]+1;
            $db->query("INSERT INTO et_favorite (content_id,sc_uid) VALUES ('$content_id','$my[user_id]')");
            $db->query("UPDATE et_users SET fav_num='$fnum' where user_id='$my[user_id]'");
        }
        $db->query("UPDATE et_users SET fav_num=fav_num+'1' where user_id='$my[user_id]'");
    }
    exit;
}

if ($act=="delfav") {
    tologin();
    $favid=$_GET['fid'];
    $tem=$db->query("DELETE FROM et_favorite WHERE fav_id='$favid' && sc_uid='$my[user_id]'");
    if ($tem==1) {
        $fnum=$my[fav_num]-1;
        $db->query("UPDATE et_users  SET fav_num='$fnum' where user_id='$my[user_id]'");
        $db->query("UPDATE et_users SET fav_num=fav_num-'1' where user_id='$my[user_id]'");
        echo "success";
        exit;
    } else {
        echo "收藏删除失败，可能网络错误或者您没有删除的权限！";
        exit;
    }
}

$i=0;
$start= ($page-1)*$home_num;
$sql= "SELECT f.fav_id,f.content_id,s.content_body,s.posttime,s.status_id,s.status_uname,s.status_type,s.type,u.user_id,u.user_name,u.user_head,t.topic_id,t.topic_body,t.open FROM et_favorite AS f,et_users AS u,et_content AS s left join et_topic AS t on s.topicid=t.topic_id && t.open=1 where s.user_id=u.user_id && s.content_id=f.content_id && f.sc_uid='$user[user_id]' order by fav_id desc limit $start,$home_num";
$query= $db->query($sql);
while($data=$db->fetch_array($query)) {
    $i=$i+1;
    $fav_id=$data['fav_id'];
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

    $favlist[] = array("fav_id"=>$fav_id,"contentid"=>$contentid,"home_uid"=>$home_uid,"home_uhead"=>$home_uhead,
        "home_uname"=>$home_uname,"content"=>$content,"posttime"=>$posttime,"statusid"=>$statusid,"status_uname"=>$status_uname,"status_type"=>$status_type,"type"=>$type,"topic_body"=>$topic_body);
}

$sql ="select count(*) AS count from et_favorite AS f,et_content AS s where s.content_id=f.content_id && f.sc_uid='$user[user_id]'";
$query = $db->query($sql);
$row = $db->fetch_array($query);
$total=$row['count'];
$pg_num=$total/$home_num;
$pg_num=intval($pg_num);
if ($total!=$pg_num*$home_num)
    $pg_num=$pg_num+1;
$np=$page+1;
$pp=$page-1;
if ($pg_num>5) {
    if ($pg_num-$page<=3)
        $page_from=($page-1)==0?1:$pg_num-4;
    else
        $page_from=($page-1)==0?1:$page-1;
    $page_end=($page_from+4)>$pg_num?$pg_num:$page_from+4;
}else{
    $page_from=1;
    $page_end=$pg_num;
}
if($my[fav_num]!=$total && $user[user_id]==$my[user_id]) {
    $db->query("update et_users set fav_num='$total' where user_id='$my[user_id]'");
}

include($template->getfile('hm_favorite.htm'));
?>