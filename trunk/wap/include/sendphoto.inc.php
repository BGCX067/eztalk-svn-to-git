<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

if (!$user_id) {
    echo "<div class='showmag'><p>您还没有登录，不能执行此操作！</p><p><a href='index.php?op=login'>现在登陆</a></p></div>";
    wapfooter();
    exit;
}


if ($action=="upload") {
    $phototitle=daddslashes(trim($_POST['phototitle']));
    if(StrLenW($phototitle)>20) {
        echo "<div class='showmag'><p>相片名称要不能大于20字符！</p><p><a href='index.php?op=sendphoto'>重新上传</a></p></div>";
        wapfooter();
        exit;
    }
    if ($_FILES['photo']['name']) {
        $refer="";
        include(ET_ROOT."/include/uploadpic.func.php");
        $ptname=date(YmdHms);
        $upname=UploadImage("photo",1,130,130,ET_ROOT."/attachments/photo/user_".$user_id."/",ET_ROOT."/attachments/photo/user_".$user_id."/",$ptname,$ptname."_thumb");

        $query = $db->query("select album_id from et_album where user_id='$user_id' && album_name='手机相册' limit 1");
        $row = $db->fetch_array($query);
        $albumid=$row['album_id'];
        if (!$albumid) {
            $db->query("INSERT INTO et_album (user_id,album_name) VALUES ('$user_id','手机相册')");
            $albumid=mysql_insert_id();
        }

        $phototitle=$phototitle?$phototitle:"$ptname";

        $db->query("INSERT INTO et_photos (al_id,user_id,pt_name,pt_title,uploadtime) VALUE ('$albumid','$user_id','$ptname','$phototitle','$addtime')");

        $upmsg="[img link=$webaddr/op/viewphoto/".mysql_insert_id()."]".$webaddr."/attachments/photo/user_".$user_id."/".$ptname."_thumb.jpg[/img]我在相册上传了一张照片：<a href=\"$webaddr/op/viewphoto/".mysql_insert_id()."\">$phototitle</a>！";

        $db->query("INSERT INTO et_content (user_id,content_body,posttime,type) VALUE ('$user_id','$upmsg','$addtime','手机')");
        $db->query("UPDATE et_users SET photo_num=photo_num+'1' where user_id='$user_id'");
        $db->query("UPDATE et_album SET photo_num=photo_num+'1' where album_id='$albumid'");

        echo "<div class='showmag'><p>照片上传成功了！</p><p><a href='index.php'>返回主页</a></p></div>";
        wapfooter();
        exit;
    }
}


echo "<h2>发照片</h2>".
"<form enctype=\"multipart/form-data\" action=\"index.php?op=sendphoto\" method=\"post\">".
"建议照片大小不超过500K。照片文件名不要包含中文。如果照片太大，建议压缩后再上传。".
"<p>相片名称：<input type=\"text\" name=\"phototitle\"/></p>".
"<p>选择照片：<input type=\"file\" name=\"photo\"/></p>".
"<p><input type=\"hidden\" name=\"action\" value=\"upload\" /><input type=\"submit\" value=\"上传\" /></p>".
"</form>";
?>