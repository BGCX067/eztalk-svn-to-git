<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

if ($act=="logout") {
    dsetcookie("wapcookie","",-2592000);
    echo "<div class='showmag'>���Ѿ��ɹ��˳���<a href=\"index.php?op=login\">������ҳ</a></div>";
    wapfooter();
    exit;
}

if($user_id) {
    echo "<div class='showmag'>���Ѿ���¼��<a href='index.php'>������ҳ</a></div>";
    wapfooter();
    exit;
}

if($action=="login") {
    $loginaccount=daddslashes(iconv("utf-8", "gbk",$_POST['loginaccount']));
    $loginpass=daddslashes($_POST['loginpass']);
    $auto_login=$_POST['auto_login'];
    $md5_password = md5(md5($loginpass));

    $query = $db->query("SELECT user_id,password FROM et_users where mailadres='$loginaccount' || user_id='$loginaccount'");
    $data = $db->fetch_array($query);
    $upass=$data['password'];
    $uid=$data['user_id'];
    if ($upass==$md5_password) {
        if ($auto_login=="on") dsetcookie('wapcookie', authcode("$uid\t$upass",'ENCODE'), 2592000);
        else dsetcookie('wapcookie', authcode("$uid\t$upass",'ENCODE'));
        echo "<div class='showmag'>��½�ɹ�,<a href='index.php'>������ҳ</a></div>";
        wapfooter();
        exit;
    } else {
        echo "<div class='showmag'><p>��½ʧ�ܣ��û������������</p><p><a href='index.php?op=login'>������ҳ</a></p></div>";
        wapfooter();
        exit;
    }
}

echo  "<form method=\"post\" action=\"index.php?op=login\" >".
"<h3>Email / Uid��</h3><p><input type=\"text\" name=\"loginaccount\" /></p>".
"<h3>��¼���룺</h3><p><input type=\"password\" name=\"loginpass\" /></p>".
"<p><input type=\"checkbox\" name=\"auto_login\" value=\"on\" checked=\"checked\" /> �´��Զ���¼</p>".
"<p><input type=\"hidden\" name=\"action\" value=\"login\" />".
"<input type=\"submit\" value=\"��¼\" /></p>".
"</form>".
"<p>����<a href=\"index.php?op=browse\">��㿴��</a></p>".
"<p>EasyTalk��һ�����㲩�͡�</p><p>��ʱ��ط���Ϣ</p><p>ʱʱ�̿̿�����</p><p>�ֻ�����ҳ��MSN��QQ</p>";
?>
