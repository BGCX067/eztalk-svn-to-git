<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

$page=$_GET['page']?$_GET['page']:"1";
$tid=$_GET['tid']?$_GET['tid']:$_POST['tid'];

$sql="select * from et_topic where topic_id='$tid'";
$query = $db->query($sql);
$data = $db->fetch_array($query);
$tpbody=$data['topic_body'];
$tpopen=$data['open'];

if (!$tpbody) header("loccation: $webaddr/home?tip=41");
if ($tpopen==0) header("loccation: $webaddr/home?tip=42");

//回复设置
$toname=$_GET['to'];
if ($toname) {
    $toid=nametoid($toname);
    $status_id =$_GET["status_id"];
    $statustype =$_GET["status_type"]?$_GET["status_type"]:"talk";
    if ($statustype=="talk") $sql = "select content_id from et_content where content_id='$status_id' && user_id='$toid'";
    elseif ($statustype=="photo") $sql = "select pt_id from et_photos where pt_id='$status_id' && user_id='$toid'";
    $query = $db->query($sql);
    $data = $db->fetch_array($query);
    if (!$data) $toname="";
}

//以下是 发送 代码
if ($action =="sendtopic") {
    tologin();
    $content = daddslashes(trim($_POST["content"]));
    $toid=$_POST["toid"];
    $status_id = $_POST["status_id"];
    $status_type = $_POST["status_type"];
    $topid = $_POST["topid"]?$_POST["topid"]:0;
    $toname=idtoname($toid);

    if (!empty($content)) {
        $content=replace($content); //词语过滤
        if ($toid && $status_id) {
           $content="@<a href=\"$webaddr/home/u.$toid\">$toname</a> $content";

           $db->query("INSERT INTO et_content (user_id,topicid,content_body,posttime,status_id,status_uid,status_uname,status_type) VALUES ('$my[user_id]','$topid','$content','$addtime','$status_id','$toid','$toname','$status_type')");
           fsock($toid,"【小T提醒】 ".$my[user_name]."回复了你的TALK，查看地址:".$webaddr."/home/replies");
        }else {
            $db->query("INSERT INTO et_content (user_id,topicid,content_body,posttime) VALUES ('$my[user_id]','$topid','$content','$addtime')");
        }
        header("location: $webaddr/op/topic/$topid");
        exit;
    } else {
        header("location: $webaddr/op/topic/$topid&tip=25");
        exit;
    }
}

$i=0;
$start= ($page-1)*$home_num;
$sql="SELECT s.content_id,s.content_body,s.posttime,s.type,s.status_id,s.status_uname,s.status_type,u.user_id,u.user_name,u.user_head,t.topic_body FROM et_users AS u,et_content AS s left join et_topic AS t on s.topicid=t.topic_id where s.user_id=u.user_id && s.topicid='$tid' order by s.content_id desc limit $start,$home_num";
$query = $db->query($sql);
while($data = $db->fetch_array($query)) {
    $i=$i+1;
    $contentid=$data['content_id'];
    $topic_uid=$data['user_id'];
    $topic_uname=$data['user_name'];
    $topic_uhead=$data['user_head']?"$webaddr/attachments/head/".$data['user_head']:"$webaddr/images/noavatar.jpg";
    $content=$data['content_body'];
    $content=ubb($content);
    $posttime=timeop($data['posttime']);
    $statusid=$data['status_id'];
    $status_uname=$data['status_uname'];
    $status_type=$data['status_type'];
    $type=$data['type'];
    $topic_body=$data['topic_body']?"<font color='red'>[".$data['topic_body']."]</font>&nbsp;":"";

    $topic[] = array("contentid"=>$contentid,"topic_uid"=>$topic_uid,"topic_uname"=>$topic_uname,"topic_uhead"=>$topic_uhead,"content"=>$content,"posttime"=>$posttime,"type"=>$type,"statusid"=>$statusid,"status_uname"=>$status_uname,"status_type"=>$status_type,"topic_body"=>$topic_body);
}

$sql ="select count(*) AS count from et_content where topicid='$tid'";
$query = $db->query($sql);
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

//模板和Foot
$web_name3="专题：[".$tpbody."]";
$sqlnum=$db->querynum;
$mtime = explode(' ', microtime());
$loadtime=$mtime[1] + $mtime[0] - $starttime;
include($template->getfile('op_topic.htm'));
?>