<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

echo "<h2>�����˵ʲô...</h2><ul>";

//��ѯ
$start= ($page-1)*10;
$sql = "SELECT s.content_id,s.content_body,s.posttime,s.type,s.status_id,s.status_uname,s.status_type,u.user_id,u.user_name FROM et_content AS s,et_users AS u where s.user_id=u.user_id order by s.content_id desc limit $start,10";
$query = $db->query($sql);
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
        echo "<li><a href='index.php?op=home&uid=$content_uid'>$content_uname</a> $content_body <span class=\"stamp\">$posttime ͨ��".$content_type."��".$status_uname."�Ļظ�</span></li>";
    } else if ($status_type=="photo") {
        echo "<li><a href='index.php?op=home&uid=$content_uid'>$content_uname</a> $content_body <span class=\"stamp\">$posttime ͨ��".$content_type."��".$status_uname."��Ƭ�Ļظ�</span></li>";
    } else {
        echo "<li><a href='index.php?op=home&uid=$content_uid'>$content_uname</a> $content_body <span class=\"stamp\">$posttime ͨ��$content_type</span></li>";
    }
}
echo  "</ul>";

//��ҳ
$query = $db->query("SELECT count(*) as count FROM et_content AS s,et_users AS u where s.user_id=u.user_id");
$row = $db->fetch_array($query);
$total=$row['count'];
$pg_num=$total/10;
$pg_num=intval($pg_num);
if ($total!=$pg_num*10) $pg_num=$pg_num+1;
$np=$page+1;
$pp=$page-1;
if ($pg_num>1) {
    echo "<div class='page'>";
    if ($pp>0) echo "<a href='index.php?op=browse&page=$pp'>��ҳ</a>&nbsp;";
    if ($np<=$pg_num) echo "<a href='index.php?op=browse&page=$np'>��ҳ</a>&nbsp;";
    echo "| ".$page."/".$pg_num;
    echo "</div>";
}

if (!$user_id) {
    echo "<h2>��û�е�½����<a href=\"index.php\">��½</a></h2>";
}
?>