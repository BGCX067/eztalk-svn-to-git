<?php
define('is_admin_path', 'yes');
include('../common.inc.php');

if ($admin_login !="yes" || $my[isadmin]!=1)
    header("Location: admin_login.php");


if($action=="addut") {
    $bgcolor=daddslashes($_POST['bgcolor']);
    $textcolor=daddslashes($_POST['textcolor']);
    $linkcolor=daddslashes($_POST['linkcolor']);
    $sidebar=daddslashes($_POST['sidebar']);
    $sidebox=daddslashes($_POST['sidebox']);
    $pictype=daddslashes($_POST['pictype']);
    $isupload=daddslashes($_POST['isupload']);

    if ($bgcolor && $textcolor && $linkcolor && $sidebar && $sidebox && $pictype) {
        $db->query("INSERT INTO et_usertemplates (theme_bgcolor,theme_pictype,theme_text,theme_link,theme_sidebar,theme_sidebox,theme_upload) VALUES ('$bgcolor','$pictype','$textcolor','$linkcolor','$sidebar','$sidebox','$isupload')");
        echo "<script>alert('模板添加成功，点击确定返回！');location.href='usertemplate.php'</script>";
        exit;
    } else {
        echo "<script>alert('信息填写不全，模板添加失败，点击确定返回！');location.href='usertemplate.php'</script>";
        exit;
    }
}

if ($act=="open") {
    $utid=$_GET['utid'];
    $db->query("UPDATE et_usertemplates SET isopen='1' where ut_id ='$utid'");
    header("location:usertemplate.php");
    exit;
}

if ($act=="close") {
    $utid=$_GET['utid'];
    $db->query("UPDATE et_usertemplates SET isopen='0' where ut_id ='$utid'");
    header("location:usertemplate.php");
    exit;
}

if ($act=="del") {
    $utid=$_GET['utid'];
    if ($utid==1) {
        echo "<script>alert('默认提供的模板不能删除，点击确定返回！');location.href='usertemplate.php'</script>";
        exit;
    } else {
        $db->query("DELETE FROM et_usertemplates where ut_id='$utid'");
        header("location:usertemplate.php");
        exit;
    }
}

if ($action=="edit") {
    $utid=$_GET['utid'];
    $bgcolor=daddslashes($_POST['bgcolor']);
    $textcolor=daddslashes($_POST['textcolor']);
    $linkcolor=daddslashes($_POST['linkcolor']);
    $sidebar=daddslashes($_POST['sidebar']);
    $sidebox=daddslashes($_POST['sidebox']);
    $pictype=daddslashes($_POST['pictype']);
    $isupload=daddslashes($_POST['isupload']);
    if ($bgcolor && $textcolor && $linkcolor && $sidebar && $sidebox && $pictype) {
        $db->query("UPDATE et_usertemplates set theme_bgcolor='$bgcolor',theme_pictype='$pictype',theme_text='$textcolor',theme_link='$linkcolor',theme_sidebar='$sidebar',theme_sidebox='$sidebox',theme_upload='$isupload' where ut_id='$utid'");
        echo "<script>alert('模板编辑成功，点击确定返回！');location.href='usertemplate.php'</script>";
        exit;
    } else {
        echo "<script>alert('信息填写不全，模板编辑失败，点击确定返回！');location.href='usertemplate.php?act=edit&utid=$utid'</script>";
        exit;
    }
}

if ($act=="edit") {
    $utid=$_GET['utid'];
    $sql = "select * from et_usertemplates where ut_id='$utid'";
    $query = $db->query($sql);
    $data= $db->fetch_array($query);
    $edit_bgcolor=$data['theme_bgcolor'];
    $edit_pictype=$data['theme_pictype'];
    $edit_text=$data['theme_text'];
    $edit_link=$data['theme_link'];
    $edit_sidebar=$data['theme_sidebar'];
    $edit_sidebox=$data['theme_sidebox'];
    $edit_upload=$data['theme_upload'];
    $isopen=$data['isopen'];
}

$sql = "select * from et_usertemplates order by ut_id";
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
    $isopen=$data['isopen'];

    $ut[] = array("ut_id"=>$ut_id,"theme_bgcolor"=>$theme_bgcolor,"theme_pictype"=>$theme_pictype,"theme_text"=>$theme_text
        ,"theme_link"=>$theme_link,"theme_sidebar"=>$theme_sidebar,"theme_sidebox"=>$theme_sidebox,"theme_upload"=>$theme_upload,"isopen"=>$isopen);
}

include($template->getfile('usertemplate.htm'));
?>