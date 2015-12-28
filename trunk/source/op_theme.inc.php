<?PHP
if(!defined('IN_ET')) {
	exit('Access Denied');
}

tologin();

if ($action=="theme") {
    $bgcolor=trim($_POST['bg']);
    $textcolor=trim($_POST['text']);
    $links=trim($_POST['links']);
    $sidebarcl=trim($_POST['sidebarcl']);
    $sidebox=trim($_POST['sidebox']);
    $pictype=$_POST['pictype'];
    $newbgurl=$_POST['newbgurl'];

    $uppictype = array("image/jpg","image/pjpeg","image/jpeg","image/gif","image/png","image/x-png","image/bmp");
    if ($_FILES['bgpicture']['name']) {
        if (!in_array($_FILES['bgpicture']['type'], $uppictype)) {
            echo "<script>alert('很抱歉，您上传的图片过大或者类型不正确！');location.href='$webaddr/op/theme'</script>";
            exit;
        }
        @unlink(ET_ROOT."/attachments/photo/user_".$my[user_id]."/themebg.jpg");
        @unlink(ET_ROOT."/attachments/photo/user_".$my[user_id]."/themebg_thumb.jpg");
        include(ET_ROOT."/include/uploadpic.class.php");
        $dest_dir=ET_ROOT.'/attachments/photo/user_'.$my[user_id];
        if (!is_dir($dest_dir))  @mkdir($dest_dir, 0777);
        $dest=$dest_dir.'/'.$_FILES['bgpicture']['name'];
        $dest2=$dest_dir."/themebg_thumb.jpg";
        $dest3=$dest_dir."/themebg.jpg";
        $imginfo=getimagesize($_FILES['bgpicture']['tmp_name']);
        $r=move_uploaded_file($_FILES['bgpicture']['tmp_name'],$dest);
        chmod($dest, 0755);
        $resizeimage = new resizeimage("$dest", "112", "72", "1","$dest2");
        $resizeimage = new resizeimage("$dest", $imginfo[0], $imginfo[1], "1","$dest3");
        @unlink("$dest");

        $pictype=$pictype?$pictype:"center";
        $bgurl="attachments/photo/user_".$my[user_id]; //不需要添加 $webaddr
    }
    $newbgurl=$bgurl?$bgurl:$newbgurl;

    $db->query("UPDATE et_users SET theme_bgcolor='$bgcolor',theme_pictype = '$pictype',theme_text='$textcolor',
    theme_link='$links',theme_sidebar='$sidebarcl',theme_sidebox='$sidebox',theme_bgurl='$newbgurl' WHERE  user_id='$my[user_id]'");

    echo "<script>location.href='$webaddr/op/theme&tip=43'</script>";
    exit;
}

$sql = "select * from et_usertemplates where isopen='1' order by ut_id";
$query = $db->query($sql);
while ($data= $db->fetch_array($query)) {
    $ut_id=$data['ut_id'];
    $theme_bgcolor=$data['theme_bgcolor'];
    $theme_pictype=$data['theme_pictype'];
    $theme_text=$data['theme_text'];
    $theme_link=$data['theme_link'];
    $theme_sidebar=$data['theme_sidebar'];
    $theme_sidebox=$data['theme_sidebox'];
    $theme_upload=$data['theme_upload'];
    $element=$theme_bgcolor.",".$theme_text.",".$theme_link.",".$theme_sidebar.",".$theme_sidebox.",".$theme_upload.",".$theme_pictype.",".$ut_id;

    $ut[] = array("ut_id"=>$ut_id,"theme_bgcolor"=>$theme_bgcolor,"theme_pictype"=>$theme_pictype,"theme_text"=>$theme_text
        ,"theme_link"=>$theme_link,"theme_sidebar"=>$theme_sidebar,"theme_sidebox"=>$theme_sidebox,"theme_upload"=>$theme_upload,"element"=>$element);
}

//模板和Foot
$web_name3="模板设置";
$sqlnum=$db->querynum;
$mtime = explode(' ', microtime());
$loadtime=$mtime[1] + $mtime[0] - $starttime;
include($template->getfile('op_theme.htm'));
?>