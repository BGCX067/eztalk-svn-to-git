<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

$ptid=$_GET['ptid'];
$page=$_GET['page']?$_GET['page']:"1";

if ($ptid) {
    $sql="SELECT p.al_id,p.user_id,p.pt_name,p.pt_title,p.uploadtime,u.isclose FROM et_photos as p,et_users as u where u.user_id=p.user_id && pt_id='$ptid'";
    $query = $db->query($sql);
    $data = $db->fetch_array($query);
    $alid=$data['al_id'];
    $pt_uid=$data['user_id'];
    $pt_name=str_replace("_thumb","",$data['pt_name']);
    $pt_title=$data['pt_title'];
    $pt_uisclose=$data['isclose'];
    $uploadtime=timeop($data['uploadtime']);

    if ($pt_uid!=$my['user_id']) {
        $isfriend=isfriend($pt_uid,$my[user_id]);
    }

    $sql="(select pt_id,pt_title from et_photos where pt_id<$ptid && user_id='$pt_uid' order by pt_id desc limit 1) union (select pt_id,pt_title from et_photos where pt_id>$ptid && user_id='$pt_uid' order by pt_id limit 1)";
    $query = $db->query($sql);
    while($data = $db->fetch_array($query)) {
        $ptt=$data['pt_title']?$data['pt_title']:"无标题";
        $show[] = array("id"=>$data['pt_id'],"name"=>$ptt);
    }

    if ($act=="fengmian") {
        $sql="SELECT pt_name FROM et_photos where pt_id='$ptid' && al_id='$alid' && user_id='$my[user_id]'";
        $query = $db->query($sql);
        $data = $db->fetch_array($query);
        if ($data) {
            $db->query("UPDATE et_album set face_photo='$data[pt_name]' where album_id='$alid' && user_id='$my[user_id]'");
            echo "<script>alert('封面设置成功！');location.href='$webaddr/op/viewphoto/$ptid'</script>";
            exit;
        } else {
            echo "<script>alert('封面设置失败，可能因为您没有这个权限！');location.href='$webaddr/op/viewphoto/$ptid'</script>";
            exit;
        }
    }

    $sql="SELECT a.*,u.user_name,u.theme_bgcolor,u.theme_pictype,u.theme_text,u.theme_link,u.theme_sidebar,u.theme_sidebox,u.theme_bgurl FROM et_album as a,et_users as u where u.user_id=a.user_id && a.album_id='$alid'";
    $query = $db->query($sql);
    $data = $db->fetch_array($query);
    $album_name=$data['album_name'];
    $ptuid=$data['user_id'];
    $ptuname=$data['user_name'];
    $face_photo=$data['face_photo']?"$webaddr/attachments/photo/user_$ptuid/".$data['face_photo']:"$webaddr/images/nophoto.jpg";
    $photo_num=$data['photo_num'];
    $user=array("user_id"=>$data['user_id'],"theme_bgcolor"=>$data['theme_bgcolor'],"theme_pictype"=>$data['theme_pictype'],"theme_text"=>$data['theme_text'],"theme_link"=>$data['theme_link'],"theme_sidebar"=>$data['theme_sidebar'],"theme_sidebox"=>$data['theme_sidebox'],"theme_bgurl"=>$data['theme_bgurl']);
}

if ($action=="rename") {
    $newpttitle=daddslashes(trim($_POST['newpttitle']));
    if(StrLenW($newpttitle)>20 || StrLenW($newpttitle)<2) {
        echo "<script>alert('相片名称要不能大于20字符或者小于2个字符！');location.href='$webaddr/op/viewphoto/$ptid&act=rename'</script>";
        exit;
    } else {
        $t=$db->query("UPDATE et_photos set pt_title='$newpttitle' where pt_id='$ptid' && user_id='$my[user_id]'");
        if ($t==1) {
            echo "<script>alert('恭喜您，修改相片名成功了！');location.href='$webaddr/op/viewphoto/$ptid'</script>";
            exit;
        } else {
            echo "<script>alert('很抱歉，修改相片名失败，可能因为您没有修改的权限！');location.href='$webaddr/op/viewphoto/$ptid'</script>";
            exit;
        }
    }
}


if ($act=="remove") {
    $sql="SELECT album_id,album_name,photo_num FROM et_album where user_id='$my[user_id]'";
    $query = $db->query($sql);
    while($data = $db->fetch_array($query)) {
        $album_id=$data['album_id'];
        $album_name=$data['album_name'];
        $photo_num=$data['photo_num'];
        $albumlisttemp="<option value='$album_id'>$album_name($photo_num)</option>";
        $albumlist=$albumlisttemp.$albumlist;
    }
}

if ($action=="remove") {
    $toalbum=$_POST['toalbum'];

    $sql="SELECT album_id FROM et_album where album_id='$toalbum' && user_id='$my[user_id]'";
    $query = $db->query($sql);
    if ($data=$db->fetch_array($query)) {
        $t=$db->query("UPDATE et_photos set al_id='$toalbum' where pt_id='$ptid' && user_id='$my[user_id]'");
        if ($t==1) {
            echo "<script>alert('照片转移成功了！');location.href='$webaddr/op/viewalbum/$alid'</script>";
            exit;
        } else {
            echo "<script>alert('照片转移失败，可能因为您没有转移的权限！');location.href='$webaddr/op/viewphoto/$ptid'</script>";
            exit;
        }
    } else {
        echo "<script>alert('照片转移失败，可能因为您没有转移的权限！');location.href='$webaddr/op/viewphoto/$ptid'</script>";
        exit;
    }
}

//模板和Foot
$web_name3="相册";
$sqlnum=$db->querynum;
$mtime = explode(' ', microtime());
$loadtime=$mtime[1] + $mtime[0] - $starttime;
include($template->getfile('op_viewphoto.htm'));
?>