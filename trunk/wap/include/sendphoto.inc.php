<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

if (!$user_id) {
    echo "<div class='showmag'><p>����û�е�¼������ִ�д˲�����</p><p><a href='index.php?op=login'>���ڵ�½</a></p></div>";
    wapfooter();
    exit;
}


if ($action=="upload") {
    $phototitle=daddslashes(trim($_POST['phototitle']));
    if(StrLenW($phototitle)>20) {
        echo "<div class='showmag'><p>��Ƭ����Ҫ���ܴ���20�ַ���</p><p><a href='index.php?op=sendphoto'>�����ϴ�</a></p></div>";
        wapfooter();
        exit;
    }
    if ($_FILES['photo']['name']) {
        $refer="";
        include(ET_ROOT."/include/uploadpic.func.php");
        $ptname=date(YmdHms);
        $upname=UploadImage("photo",1,130,130,ET_ROOT."/attachments/photo/user_".$user_id."/",ET_ROOT."/attachments/photo/user_".$user_id."/",$ptname,$ptname."_thumb");

        $query = $db->query("select album_id from et_album where user_id='$user_id' && album_name='�ֻ����' limit 1");
        $row = $db->fetch_array($query);
        $albumid=$row['album_id'];
        if (!$albumid) {
            $db->query("INSERT INTO et_album (user_id,album_name) VALUES ('$user_id','�ֻ����')");
            $albumid=mysql_insert_id();
        }

        $phototitle=$phototitle?$phototitle:"$ptname";

        $db->query("INSERT INTO et_photos (al_id,user_id,pt_name,pt_title,uploadtime) VALUE ('$albumid','$user_id','$ptname','$phototitle','$addtime')");

        $upmsg="[img link=$webaddr/op/viewphoto/".mysql_insert_id()."]".$webaddr."/attachments/photo/user_".$user_id."/".$ptname."_thumb.jpg[/img]��������ϴ���һ����Ƭ��<a href=\"$webaddr/op/viewphoto/".mysql_insert_id()."\">$phototitle</a>��";

        $db->query("INSERT INTO et_content (user_id,content_body,posttime,type) VALUE ('$user_id','$upmsg','$addtime','�ֻ�')");
        $db->query("UPDATE et_users SET photo_num=photo_num+'1' where user_id='$user_id'");
        $db->query("UPDATE et_album SET photo_num=photo_num+'1' where album_id='$albumid'");

        echo "<div class='showmag'><p>��Ƭ�ϴ��ɹ��ˣ�</p><p><a href='index.php'>������ҳ</a></p></div>";
        wapfooter();
        exit;
    }
}


echo "<h2>����Ƭ</h2>".
"<form enctype=\"multipart/form-data\" action=\"index.php?op=sendphoto\" method=\"post\">".
"������Ƭ��С������500K����Ƭ�ļ�����Ҫ�������ġ������Ƭ̫�󣬽���ѹ�������ϴ���".
"<p>��Ƭ���ƣ�<input type=\"text\" name=\"phototitle\"/></p>".
"<p>ѡ����Ƭ��<input type=\"file\" name=\"photo\"/></p>".
"<p><input type=\"hidden\" name=\"action\" value=\"upload\" /><input type=\"submit\" value=\"�ϴ�\" /></p>".
"</form>";
?>