<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

if (!$user_id) {
    echo "<div class='showmag'><p>����û�е�¼������ִ�д˲�����</p><p><a href='index.php?op=login'>���ڵ�½</a></p></div>";
    wapfooter();
    exit;
}

//ɾ����ʾ
if ($act=="delshare") {
    $sid=$_GET['sid'];
    $sql = "select user_id from et_content where content_id='$sid'";
    $query = $db->query($sql);
    $data = $db->fetch_array($query);
    if ($data['user_id']==$user_id) {
        echo "<div class='showmag'><p>�Ƿ�ȷ��ɾ������TALK��</p><p><a href='index.php?act=delshareok&sid=$sid'>ȷ��</a> <a href='index.php'>ȡ��</a></p></div>";
        wapfooter();
        exit;
    } else {
        echo "<div class='showmag'><p>����Ȩɾ������TALK��</p><p><a href='index.php'>������ҳ</a></p></div>";
        wapfooter();
        exit;
    }
}

//ɾ��ȷ��
if ($act=="delshareok") {
    $sid=$_GET['sid'];
    $sql = "select user_id from et_content where content_id='$sid'";
    $query = $db->query($sql);
    $data = $db->fetch_array($query);
    if ($data['user_id']==$user_id) {
        $db->query("DELETE FROM et_content WHERE user_id='$user_id' && content_id='$sid'");
        echo "<div class='showmag'><p>ɾ��TALK�ɹ���</p><p><a href='index.php'>������ҳ</a></p></div>";
        wapfooter();
        exit;
    } else {
        echo "<div class='showmag'><p>����Ȩɾ������TALK��</p><p><a href='index.php'>������ҳ</a></p></div>";
        wapfooter();
        exit;
    }
}

//����TALK
if($action=="post") {
	$cbody=daddslashes(iconv("utf-8", "gbk",trim($_POST['cbody'])));
    if ($user_id) {
        if (empty($cbody)) {
            echo "<div class='showmag'><p>��û����д���͵�����</p><a href='index.php'>������ҳ</a></p></div>";
            wapfooter();
            exit;
        } else {
            $res=$db->query("INSERT INTO et_content (user_id,content_body,posttime,type) VALUES ('$user_id','$cbody','$addtime','�ֻ�')");
            if ($res==1) {
                echo "<div class='showmag'><p>��ϲ��������ɹ���</p><p><a href='index.php'>������ҳ</a></p></div>";
                wapfooter();
                exit;
            } else {
                echo "<div class='showmag'><p>�ܱ�Ǹ������ʧ�ܣ�</p><p><a href='index.php'>������ҳ</a></p></div>";
                wapfooter();
                exit;
            }
        }
	} else {
        echo "<div class='showmag'><p>����ʧ�ܣ�����û�е�½�ɹ������������֧�֣�</p><p><a href='index.php'>������ҳ</a></p></div>";
        wapfooter();
        exit;
	}
}

//ҳ������
echo "<h2>������ʲô��</h2>".
"<form method=\"post\" action=\"index.php\">".
"<p><input type=\"text\" name=\"cbody\" value=\"\" maxlength=\"140\" /></p>".
"<p><input type=\"hidden\" name=\"action\" value=\"post\" /><input type=\"submit\" value=\"����\" /></p>".
"</form>".
"<h2>������Ϣ(<a href='index.php'>ˢ��</a>) | <a href='index.php?op=myfriends'>��ע��̬</a></h2>".
"<ul>";

$start= ($page-1)*10;
$query = $db->query("SELECT s.content_id,s.content_body,s.posttime,s.type,s.status_id,s.status_uname,s.status_type,u.user_id,u.user_name FROM et_content AS s,et_users AS u where s.user_id=u.user_id && s.user_id='$user_id' order by s.content_id desc limit $start,10");
while($data = $db->fetch_array($query)) {
    $content_id=$data['content_id'];
    $content_uid=$data['user_id'];
    $content_uname=$data['user_name'];
    $content_body=$data['content_body'];
    $content_body = str_replace("home/u.","wap/index.php?op=home&uid=",$content_body);
    $content_body=urlreplace($content_body);
    $posttime=timeop($data['posttime']);
    $content_type=$data['type'];
    $statusid=$data['status_id'];
    $status_uname=$data['status_uname'];
    $status_type=$data['status_type'];

    if ($status_type=="talk") {
        echo "<li><a href='index.php?op=home&uid=$content_uid'>$content_uname</a> $content_body <span class=\"stamp\">$posttime ͨ��".$content_type."��".$status_uname."�Ļظ� <a href=\"index.php?act=delshare&sid=$content_id\">ɾ��</a></span></li>";
    } else if ($status_type=="photo") {
        echo "<li><a href='index.php?op=home&uid=$content_uid'>$content_uname</a> $content_body <span class=\"stamp\">$posttime ͨ��".$content_type."��".$status_uname."��Ƭ�Ļظ� <a href=\"index.php?act=delshare&sid=$content_id\">ɾ��</a></span></li>";
    } else {
        echo "<li><a href='index.php?op=home&uid=$content_uid'>$content_uname</a> $content_body <span class=\"stamp\">$posttime ͨ��$content_type <a href=\"index.php?act=delshare&sid=$content_id\">ɾ��</a></span></li>";
    }
}
echo "</ul>";

//��ҳ
$query = $db->query("SELECT count(*) as count FROM et_content AS s,et_users AS u where s.user_id=u.user_id && s.user_id='$user_id'");
$row = $db->fetch_array($query);
$total=$row['count'];
$pg_num=$total/10;
$pg_num=intval($pg_num);
if ($total!=$pg_num*10) $pg_num=$pg_num+1;
$np=$page+1;
$pp=$page-1;
if ($pg_num>1) {
    echo "<div class='page'>";
    if ($pp>0) echo "<a href='index.php?op=index&page=$pp'>��ҳ</a>&nbsp;";
    if ($np<=$pg_num) echo "<a href='index.php?op=index&page=$np'>��ҳ</a>&nbsp;";
    echo "| ".$page."/".$pg_num;
    echo "</div>";
}
?>