<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

tologin();

$uid=isset($_GET['uid'])?$_GET['uid']:$my[user_id];
$uid=$_POST["fuid"]?$_POST["fuid"]:$uid;
$isfriend=isfriend($uid,$my[user_id]);

$sql = "select user_name,user_head,issendmsg,theme_bgcolor,theme_pictype,theme_text,theme_link,theme_sidebar,theme_sidebox,theme_bgurl from et_users where user_id='$uid'";
$query = $db->query($sql);
$row= $db->fetch_array($query);
$uname =$row['user_name'];
$uhead=$row['user_head']?"$webaddr/attachments/head/".$row['user_head']:"$webaddr/images/noavatar.jpg";
$uissendmsg =$row['issendmsg'];
$user=array("user_id"=>$uid,"theme_bgcolor"=>$data['theme_bgcolor'],"theme_pictype"=>$data['theme_pictype'],"theme_text"=>$data['theme_text'],"theme_link"=>$data['theme_link'],"theme_sidebar"=>$data['theme_sidebar'],"theme_sidebox"=>$data['theme_sidebox'],"theme_bgurl"=>$data['theme_bgurl']);

if(!$uname) {
    echo "<script>alert(\"��ʾ��û���ҵ����û����뷵����ҳ��\");window.location.href='$webaddr/index';</script>";
    exit;
}

if($action=="send") {
	$content = daddslashes(trim($_POST["content"]));
    if(!$uid) {
        if($refer) header("location: ".$refer."&tip=19");
        else echo "<script>alert(\"��ʾ�������͵��û������ڣ�\");window.location.href='$prev';</script>";
        exit;
    }
    if ($uissendmsg==1 && $isfriend[allfri]==0 && $uid!=$my[user_id]) {
        if($refer) header("location: ".$refer."&tip=20");
        else echo "<script>alert(\"��ʾ���ܱ�Ǹ�����û�������˽���ã�ֻ���ܺ��ѵ�˽�ţ�\");window.location.href='$prev';</script>";
        exit;
    }
	if (!empty($content)) {
        $content=replace($content);
	    $db->query("INSERT INTO et_messages (js_id,fs_id,message_body,m_time) VALUES ('$uid','$my[user_id]','$content','$addtime')");
        fsock($uid,"��СT���ѡ� ".$my[user_name]."���㷢����һ��˽�ţ��鿴��ַ:".$webaddr."/home/privatemsg");
        if($refer) header("location: ".$refer."&tip=22");
        else echo "<script>alert(\"��ʾ��˽���Ѿ����ͳɹ���\");window.location.href='$prev';</script>";
        exit;
    } else {
        if($refer) header("location: ".$refer."&tip=24");
        else echo "<script>alert(\"��ʾ����û����д���͵����ݣ�����������д��\");window.location.href='$prev';</script>";
        exit;
    }
}

//ģ���Foot
$sqlnum=$db->querynum;
$mtime = explode(' ', microtime());
$loadtime=$mtime[1] + $mtime[0] - $starttime;
include($template->getfile('op_sendmsg.htm'));
?>