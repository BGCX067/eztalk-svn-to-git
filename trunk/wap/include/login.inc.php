<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

if ($act=="logout") {
    dsetcookie("wapcookie","",-2592000);
    echo "<div class='showmag'>您已经成功退出！<a href=\"index.php?op=login\">返回首页</a></div>";
    wapfooter();
    exit;
}

if($user_id) {
    echo "<div class='showmag'>您已经登录！<a href='index.php'>返回主页</a></div>";
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
        echo "<div class='showmag'>登陆成功,<a href='index.php'>返回主页</a></div>";
        wapfooter();
        exit;
    } else {
        echo "<div class='showmag'><p>登陆失败，用户名或密码错误！</p><p><a href='index.php?op=login'>返回主页</a></p></div>";
        wapfooter();
        exit;
    }
}

echo  "<form method=\"post\" action=\"index.php?op=login\" >".
"<h3>Email / Uid：</h3><p><input type=\"text\" name=\"loginaccount\" /></p>".
"<h3>登录密码：</h3><p><input type=\"password\" name=\"loginpass\" /></p>".
"<p><input type=\"checkbox\" name=\"auto_login\" value=\"on\" checked=\"checked\" /> 下次自动登录</p>".
"<p><input type=\"hidden\" name=\"action\" value=\"login\" />".
"<input type=\"submit\" value=\"登录\" /></p>".
"</form>".
"<p>或者<a href=\"index.php?op=browse\">随便看看</a></p>".
"<p>EasyTalk是一个迷你博客。</p><p>随时随地发消息</p><p>时时刻刻看朋友</p><p>手机、网页、MSN、QQ</p>";
?>
