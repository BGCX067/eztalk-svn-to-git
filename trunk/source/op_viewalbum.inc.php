<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

$alid=$_GET['alid'];
$page=$_GET['page']?$_GET['page']:"1";

if (!$alid) {
    echo "<script>alert('很抱歉，发生错误，请返回相册主页！');location.href='$webaddr/op/photo'</script>";
    exit;
}

$sql="SELECT a.*,u.user_name,u.theme_bgcolor,u.theme_pictype,u.theme_text,u.theme_link,u.theme_sidebar,u.theme_sidebox,u.theme_bgurl,u.isclose FROM et_album as a,et_users as u where u.user_id=a.user_id && a.album_id='$alid'";
$query = $db->query($sql);
$data = $db->fetch_array($query);
$album_name=$data['album_name'];
$ptuid=$data['user_id'];
$face_photo=$data['face_photo']?"$webaddr/attachments/photo/user_$ptuid/".$data['face_photo']:"$webaddr/images/nophoto.jpg";
$photo_num=$data['photo_num'];
$ptuname=$data['user_name'];
$ptuisclose=$data['isclose'];
$user=array("user_id"=>$data['user_id'],"theme_bgcolor"=>$data['theme_bgcolor'],"theme_pictype"=>$data['theme_pictype'],"theme_text"=>$data['theme_text'],"theme_link"=>$data['theme_link'],"theme_sidebar"=>$data['theme_sidebar'],"theme_sidebox"=>$data['theme_sidebox'],"theme_bgurl"=>$data['theme_bgurl']);

if ($ptuid!=$my['user_id']) {
    $isfriend=isfriend($ptuid,$my[user_id]);
}

if ($act=="delphoto") {
    $ptid=$_GET['ptid'];

    $sql="SELECT pt_name  FROM et_photos where pt_id='$ptid'";
    $query = $db->query($sql);
    $data = $db->fetch_array($query);
    $t=$db->query("DELETE FROM et_photos WHERE pt_id ='$ptid' && user_id='$my[user_id]'");
    if ($t==1) {
        @unlink(ET_ROOT."/attachments/photo/user_$my[user_id]/$data[pt_name].jpg");
        @unlink(ET_ROOT."/attachments/photo/user_$my[user_id]/$data[pt_name]_thumb.jpg");
        $db->query("UPDATE et_users SET photo_num=photo_num-'1' where user_id='$my[user_id]'");
        echo "success";
        exit;
    } else {
        echo "很抱歉，删除相片失败了，可能您没有删除的权限！";
        exit;
    }
}

if ($action=="edit") {
    $newalbumname=daddslashes(trim($_POST['newalbumname']));
    if(StrLenW($newalbumname)>20 || StrLenW($newalbumname)<2) {
        echo "<script>alert('相册名称要不能大于20字符或者小于2个字符！');location.href='$webaddr/op/viewalbum/$alid'</script>";
        exit;
    } else {
        $t=$db->query("UPDATE et_album set album_name='$newalbumname' where album_id='$alid' && user_id='$my[user_id]'");
        if ($t==1) {
            echo "<script>alert('恭喜您，修改相册名成功了！');location.href='$webaddr/op/viewalbum/$alid'</script>";
            exit;
        } else {
            echo "<script>alert('很抱歉，修改相册名失败，可能因为您没有修改的权限！');location.href='$webaddr/op/viewalbum/$alid'</script>";
            exit;
        }
    }
}

if ($action=="upload") {
    $phototitle=daddslashes(trim($_POST['phototitle']));
    if(StrLenW($phototitle)>20) {
        echo "<script>alert('相片名称要不能大于20字符！');location.href='$webaddr/op/viewalbum/$alid&act=upload'</script>";
        exit;
    }
    if ($_FILES['photo']['name']) {
        $refer=$webaddr."/op/viewalbum/".$alid;
        include(ET_ROOT."/include/uploadpic.func.php");
        $ptname=date(YmdHms);
        $upname=UploadImage("photo",1,130,130,ET_ROOT."/attachments/photo/user_".$my[user_id]."/",ET_ROOT."/attachments/photo/user_".$my[user_id]."/",$ptname,$ptname."_thumb");

        $phototitle=$phototitle?$phototitle:"$ptname";

        $db->query("INSERT INTO et_photos (al_id,user_id,pt_name,pt_title,uploadtime) VALUE ('$alid','$my[user_id]','$upname','$phototitle','$addtime')");

        $upmsg="[img link=$webaddr/op/viewphoto/".mysql_insert_id()."]".$webaddr."/attachments/photo/user_".$my[user_id]."/".$upname."[/img]我在相册上传了一张照片：<a href=\"$webaddr/op/viewphoto/".mysql_insert_id()."\">$phototitle</a>！";

        $db->query("INSERT INTO et_content (user_id,content_body,posttime) VALUE ('$my[user_id]','$upmsg','$addtime')");
        $db->query("UPDATE et_users SET photo_num=photo_num+'1' where user_id='$my[user_id]'");

        echo "<script>alert('照片上传成功了！');location.href='$webaddr/op/viewalbum/$alid'</script>";
        exit;
    }
}

$i=0;
$start= ($page-1)*16;
$sql="SELECT pt_id,user_id,pt_name,pt_title,uploadtime FROM et_photos where al_id='$alid' order by pt_id desc limit $start,16";
$query = $db->query($sql);
while($data = $db->fetch_array($query)) {
    $i=$i+1;
    $pt_id=$data['pt_id'];
    $user_id=$data['user_id'];
    $pt_name=$data['pt_name'];
    $pt_title=$data['pt_title'];
    $time=timeop($data['uploadtime']);

    $photo[] = array("pt_id"=>$pt_id,"user_id"=>$user_id,"pt_name"=>$pt_name,"pt_title"=>$pt_title,"time"=>$time);
}

$query = $db->query("select count(*) as count from et_photos where al_id='$alid'");
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
if($photo_num!=$total && $ptuid==$my[user_id]) {
    $db->query("update et_album set photo_num='$total' where album_id='$alid' && user_id='$my[user_id]'");
}

//模板和Foot
$web_name3="相册";
$sqlnum=$db->querynum;
$mtime = explode(' ', microtime());
$loadtime=$mtime[1] + $mtime[0] - $starttime;
include($template->getfile('op_viewalbum.htm'));
?>