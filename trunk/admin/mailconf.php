<?PHP
define('is_admin_path', 'yes');
include('../common.inc.php');

if ($admin_login !="yes" || $my[isadmin]!=1)
    header("Location: admin_login.php");

if ($action=="save") {
    $cmail_server=daddslashes(trim($_POST['mail_server']));
    $cmail_port=daddslashes(trim($_POST['mail_port']));
    $cmail_name=daddslashes(trim($_POST['mail_name']));
    $cmail_user=daddslashes(trim($_POST['mail_user']));
    $cmail_pass=daddslashes(trim($_POST['mail_pass']));

    $db->query("UPDATE et_settings  SET mail_server = '$cmail_server',mail_port='$cmail_port',mail_name='$cmail_name',mail_user='$cmail_user',mail_pass='$cmail_pass'");
    @unlink(ET_ROOT."/include/cache/setting.cache.php");

    echo "<script>alert(\"提示：邮件服务器配置完成!\");window.location.href='mailconf.php';</script>";
    exit;
}

include($template->getfile('mailconf.htm'));
?>