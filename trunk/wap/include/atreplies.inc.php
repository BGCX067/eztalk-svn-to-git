<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

if (!$user_id) {
    echo "<div class='showmag'><p>您还没有登录，不能执行此操作！</p><p><a href='index.php?op=login'>现在登陆</a></p></div>";
    wapfooter();
    exit();
}

echo "<h2>@我 (<a href='index.php?op=atreplies'>刷新</a>)</h2><ul>";

//查询
$start= ($page-1)*10;
$query = $db->query("SELECT s.content_id,s.content_body,s.posttime,s.type,s.status_id,s.status_uname,s.status_type,u.user_id,u.user_name FROM et_content AS s,et_users AS u where s.user_id=u.user_id && s.status_uid='$user_id' order by s.content_id desc limit $start,10");
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
        echo "<li><a href='index.php?op=home&uid=$content_uid'>$content_uname</a> $content_body <span class=\"stamp\">$posttime 通过".$content_type."给".$status_uname."的回复</span></li>";
    } else if ($status_type=="photo") {
        echo "<li><a href='index.php?op=home&uid=$content_uid'>$content_uname</a> $content_body <span class=\"stamp\">$posttime 通过".$content_type."给".$status_uname."相片的回复</span></li>";
    } else {
        echo "<li><a href='index.php?op=home&uid=$content_uid'>$content_uname</a> $content_body <span class=\"stamp\">$posttime 通过$content_type</span></li>";
    }
}
echo "</ul>";

//分页
$query = $db->query("select count(*) as count from et_content where status_uid='$user_id'");
$row = $db->fetch_array($query);
$total=$row['count'];
$pg_num=$total/10;
$pg_num=intval($pg_num);
if ($total!=$pg_num*10) $pg_num=$pg_num+1;
$np=$page+1;
$pp=$page-1;
if ($pg_num>1) {
    echo "<div class='page'>";
    if ($pp>0) echo "<a href='index.php?op=atreplies&page=$pp'>上页</a>&nbsp;";
    if ($np<=$pg_num) echo "<a href='index.php?op=atreplies&page=$np'>下页</a>&nbsp;";
    echo "| ".$page."/".$pg_num;
    echo "</div>";
}
?>