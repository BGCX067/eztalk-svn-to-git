<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

if (!$user_id) {
    echo "<div class='showmag'><p>����û�е�¼������ִ�д˲�����</p><p><a href='index.php?op=login'>���ڵ�½</a></p></div>";
    wapfooter();
    exit();
}

$pmtp=$_GET['pmtp']?$_GET['pmtp']:"my";

//����
if ($pmtp=="my") echo "<h2>���յ���˽�� | <a href='index.php?op=privatemsg&pmtp=send'>�ҷ�����˽��</a></h2>";
else if ($pmtp=="send") echo "<h2><a href='index.php?op=privatemsg'>���յ���˽��</a> | �ҷ�����˽��</h2>";

//ɾ��ȷ��
if($act=="delpmsg") {
    $mid = trim($_GET["mid"]);
    echo "<div class='showmag'><p>�Ƿ�ȷ��ɾ������˽�ţ�</p><p><a href='index.php?op=privatemsg&act=delpmsgok&mid=$mid'>ȷ��</a> <a href='index.php?op=privatemsg'>ȡ��</a></p></div>";
    wapfooter();
    exit();
}

//ɾ��OK
if($act=="delpmsgok") {
    $mid = trim($_GET["mid"]);
    $tm=$db->query("DELETE FROM et_messages WHERE (js_id ='$user_id' || fs_id='$user_id') && message_id='$mid'");
    if ($tm==1) {
        echo "<div class='showmag'><p>��ϲ����˽��ɾ���ɹ���</p><p><a href='index.php?op=privatemsg'>����˽��ҳ��</a></p></div>";
        wapfooter();
        exit();
    } else {
        echo "<div class='showmag'><p>�ܱ�Ǹ��˽��ɾ��ʧ�ܣ�</p><p><a href='index.php?op=privatemsg'>����˽��ҳ��</a></p></div>";
        wapfooter();
        exit();
    }
}

//��ѯ
$start= ($page-1)*10;
if ($pmtp=="my") {
    $sql = "select m.message_id,m.message_body,m.m_time,u.user_id,u.user_name from et_messages AS m,et_users AS u where m.fs_id=u.user_id && m.js_id='$user_id' order by m.message_id desc limit $start,10";
} else {
    $sql = "select m.message_id,m.message_body,m.m_time,u.user_id,u.user_name from et_messages AS m,et_users AS u where m.js_id=u.user_id && m.fs_id='$user_id' order by m.message_id desc limit $start,10";
}
$query = $db->query($sql);
while ($data=$db->fetch_array($query)) {
    $i=$i+1;
    $contentid=$data['message_id'];
    $home_uid=$data['user_id'];
    $home_uname=$data['user_name'];
    $content = str_replace("home/u.","wap/index.php?op=home&uid=",$data['message_body']);
    $content=urlreplace($content);
    $posttime=timeop($data['m_time']);

    if ($pmtp=="my") echo "<li>����:";
    else echo "<li>����:";
    echo "<a href='index.php?op=home&uid=$home_uid'>$home_uname</a> $content <span class=\"stamp\">$posttime <a href=\"index.php?op=privatemsg&act=delpmsg&mid=$contentid\">ɾ��</a></span></li>";
}
echo "</ul>";

//��ҳ
if ($pmtp=="my") {
    $query = $db->query("select count(*) as count from et_messages AS m,et_users AS u where m.fs_id=u.user_id && m.js_id='$user_id'");
} else {
    $query = $db->query("select count(*) as count from et_messages AS m,et_users AS u where m.js_id=u.user_id && m.fs_id='$user_id'");
}
$row = $db->fetch_array($query);
$total=$row['count'];
$pg_num=$total/10;
$pg_num=intval($pg_num);
if ($total!=$pg_num*10) $pg_num=$pg_num+1;
$np=$page+1;
$pp=$page-1;
if ($pg_num>1) {
    echo "<div class='page'>";
    if ($pp>0) echo "<a href='index.php?op=privatemsg&pmtp=$pmtp&page=$pp'>��ҳ</a>&nbsp;";
    if ($np<=$pg_num) echo "<a href='index.php?op=privatemsg&pmtp=$pmtp&page=$np'>��ҳ</a>&nbsp;";
    echo "| ".$page."/".$pg_num;
    echo "</div>";
}
?>