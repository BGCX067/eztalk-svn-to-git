<?PHP
if(!defined('IN_ET')) {
	exit('Access Denied');
}

if ($act=="logout") {
    dsetcookie('authcookie', '', -31536000);
    dsetcookie('admin_login','', -1800);
    header("location: $webaddr/index?tip=14");
    exit;
}

tohome();
$urlrefer=$_COOKIE["urlrefer"];

if ($action=="login") {
    $login_mail= daddslashes($_POST["mailadres"]);
    $pass = daddslashes(md5(md5($_POST["password"])));
    $rememberMe = $_POST["rememberMe"];

    $sql = "Select last_login,userlock,user_id,mailadres from et_users where (mailadres='$login_mail' || user_id='$login_mail') && password='$pass'";
    $query = $db->query($sql);
    if ($row = $db->fetch_array($query)){
        dsetcookie('urlrefer',''); // 销毁登录跳转cookie
        if ($row["userlock"]==1) {
            header("location: index?tip=11");
            exit;
        } else {
            $login_mail=$row["mailadres"];
            if ($rememberMe=="on") {
                dsetcookie('authcookie', authcode("$pass\t$login_mail\t$row[user_id]",'ENCODE'), 31536000);
            } else {
                dsetcookie('authcookie', authcode("$pass\t$login_mail\t$row[user_id]",'ENCODE'));
            }
            if ($row["last_login"]==0) {
                header("location: $webaddr/op/setting&tip=12");
                exit;
            } else {
                if ($urlrefer) {
                    header("location: $urlrefer");
                    exit;
                } else {
                    header("location: $webaddr/home");
                    exit;
                }
            }
        }
    } else {
        header("location: $webaddr/op/login&tip=13");
        exit;
    }
}

//模板和Foot
$web_name3="用户登录";
$sqlnum=$db->querynum;
$mtime = explode(' ', microtime());
$loadtime=$mtime[1] + $mtime[0] - $starttime;
include($template->getfile('op_login.htm'));
?>