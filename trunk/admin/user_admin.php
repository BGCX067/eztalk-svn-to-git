<?php
define('is_admin_path', 'yes');
include('../common.inc.php');

if ($admin_login !="yes" || $my[isadmin]!=1)
    header("Location: admin_login.php");

if ($act=="search") {
    $u_name = $_GET["u_name"];
    $u_id = $_GET["u_id"];
    $sql_us2 = "select * from et_users where user_name='$u_name' or user_id='$u_id'";
    $query_us2 = $db->query($sql_us2);
    $row = $db->fetch_array($query_us2);
    $new_user_id=$row['user_id'];
    $new_user_name=$row['user_name'];
    $new_user_mail=$row['mailadres'];
    $new_isadmin=$row['isadmin'];
    $new_userlock =$row['userlock'];

    if (!$new_user_id) {
        echo "<script>alert(\"提示：该会员不存在！\");window.location.href='user_admin.php';</script>";
        exit;
    }
}

if ($action=="user_edit") {
    $edit_id = $_POST["edit_id"];
    $edit_pass = $_POST["edit_pass"];
    $edit_email = $_POST["edit_email"];
    $edit_admin = $_POST["edit_admin"];
    $edit_close = $_POST["edit_close"];

    if (!empty($edit_pass)) {
        $db->query("UPDATE et_users  SET password='".md5(md5($edit_pass))."',mailadres='$edit_email',isadmin='$edit_admin',userlock='$edit_lock' where user_id='$edit_id'");
    } else {
        $db->query("UPDATE et_users  SET mailadres='$edit_email',isadmin='$edit_admin',userlock='$edit_lock' where user_id='$edit_id'");
    }
    echo "<script>alert(\"提示：会员资料修改成功！\");window.location.href='user_admin.php?u_id=$edit_id&act=search';</script>";
    exit;
}

include($template->getfile('user_admin.htm'));
?>


