<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

$uid=$_GET['uid']?$_GET['uid']:$my["user_id"];
$page=$_GET['page']?$_GET['page']:"1";

if ($uid) {
    $sql = "select user_name,user_head,isclose,theme_bgcolor,theme_pictype,theme_text,theme_link,theme_sidebar,theme_sidebox,theme_bgurl from et_users where user_id='$uid'";
    $query = $db->query($sql);
    $data= $db->fetch_array($query);
    $ptuname=$data['user_name'];
    $ptuisclose=$data['isclose'];
    $ptuhead=$data['user_head']?"$webaddr/attachments/head/".$data['user_head']:"$webaddr/images/noavatar.jpg";
    $user=array("user_id"=>$uid,"theme_bgcolor"=>$data['theme_bgcolor'],"theme_pictype"=>$data['theme_pictype'],"theme_text"=>$data['theme_text'],"theme_link"=>$data['theme_link'],"theme_sidebar"=>$data['theme_sidebar'],"theme_sidebox"=>$data['theme_sidebox'],"theme_bgurl"=>$data['theme_bgurl']);

    if ($uid!=$my['user_id']) {
        $isfriend=isfriend($uid,$my[user_id]);
    }
    if(!$ptuname) {
        echo "<script>alert('�ܱ�Ǹ��û���ҵ���Ҫ���ʵ��û���');location.href='$webaddr/op/photo/u.$my[user_id]'</script>";
        exit;
    }
}

if ($action=="creatalbum") {
    $albumname=daddslashes(trim($_POST['albumname']));
    if(StrLenW($albumname)>20 || StrLenW($albumname)<2) {
        echo "<script>alert('�������Ҫ���ܴ���20�ַ�����С��2���ַ���');location.href='$webaddr/op/photo/u.$my[user_id]&act=creatalbum'</script>";
        exit;
    } else {
        $db->query("INSERT INTO et_album (user_id,album_name) VALUES ('$my[user_id]','$albumname')");
        echo "<script>alert('��ϲ������� $albumname �����ɹ������ȷ�����������ҳ��');location.href='$webaddr/op/photo/u.$my[user_id]'</script>";
        exit;
    }
}

if ($act=="delalbum") {
    $alid=$_GET['alid'];
    $query = $db->query("select count(*) as count from et_photos where al_id='$alid'");
    $row = $db->fetch_array($query);
    $total=$row['count'];
    if ($total!=0) {
        echo "�ܱ�Ǹ������᲻Ϊ�գ��뽫�����պ���ɾ����";
        exit;
    } else {
        $t=$db->query("DELETE FROM et_album WHERE album_id='$alid' && user_id='$my[user_id]'");
        if ($t==1) {
            echo "success";
            exit;
        } else {
            echo "�ܱ�Ǹ��ɾ�����ʧ���ˣ�������·���������û��ɾ����Ȩ�ޣ�";
            exit;
        }
    }
}

$i=0;
$start= ($page-1)*16;
$sql="SELECT album_id,album_name,face_photo,photo_num FROM et_album where user_id='$uid' order by album_id desc limit $start,16";
$query = $db->query($sql);
while($data = $db->fetch_array($query)) {
    $i=$i+1;
    $album_id=$data['album_id'];
    $album_name=$data['album_name'];
    $face_photo=$data['face_photo']?"$webaddr/attachments/photo/user_$uid/".$data['face_photo']:"$webaddr/images/nophoto.jpg";
    $photo_num=$data['photo_num'];

    $photo[] = array("album_id"=>$album_id,"album_name"=>$album_name,"face_photo"=>$face_photo,"photo_num"=>$photo_num);
}

$query = $db->query("select count(*) as count from et_album where user_id='$uid'");
$row = $db->fetch_array($query);
$total=$row['count'];
$pg_num=$total/16;
$pg_num=intval($pg_num);
if ($total!=$pg_num*16)
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

$query = $db->query("select count(*) as count from et_photos where user_id='$uid'");
$row = $db->fetch_array($query);
$pttotal=$row['count'];
if($my[photo_num]!=$pttotal && $uid==$my[user_id]) {
    $db->query("update et_users set photo_num='$pttotal' where user_id='$my[user_id]'");
}

//ģ���Foot
$web_name3="���";
$sqlnum=$db->querynum;
$mtime = explode(' ', microtime());
$loadtime=$mtime[1] + $mtime[0] - $starttime;
include($template->getfile('op_photo.htm'));
?>