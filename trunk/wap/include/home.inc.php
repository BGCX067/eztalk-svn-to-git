<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

if (!$user_id) {
    echo "<div class='showmag'><p>您还没有登录，不能执行此操作！</p><p><a href='index.php?op=login'>现在登陆</a></p></div>";
    wapfooter();
    exit;
}

$uid=$_GET['uid']?$_GET['uid']:$user_id;
$refer=$_GET['refer'];

//添加关注
if ($act =="guanzhu") {
    if(!idtoname($uid)) {
        echo "<div class='showmag'><p>该用户不存在或者已经被管理员删除！</p><p><a href='$refer'>返回上一页</a></p></div>";
        wapfooter();
        exit;
    }
    $isfriend=isfriend($uid,$user_id);
    if($isfriend['fri']==1) {
        echo "<div class='showmag'><p>您已经关注过此用户！</p><p><a href='$refer'>返回上一页</a></p></div>";
        wapfooter();
        exit;
    }
    if ($isfriend['fri']==0) {
        $msg="$my[user_name] 关注了你，你也去关注他吧，只有相互关注了才能成为好友哦！";
        $db->query("INSERT INTO et_messages  (js_id,fs_id,message_body,m_time) VALUES ('$uid','$user_id','$msg','$addtime')");
        $db->query("INSERT INTO et_friend  (fid_jieshou,fid_fasong) VALUES ('$uid','$user_id')");

        frinum($user_id);
        frinum($uid);

        echo "<div class='showmag'><p>关注好友成功！</p><p><a href='$refer'>返回上一页</a></p></div>";
        wapfooter();
        exit;
    }
}

//删除关注
if ($act =="jiechu") {
    if(!idtoname($uid)) {
        echo "<div class='showmag'><p>该用户不存在或者已经被管理员删除！</p><p><a href='$refer'>返回上一页</a></p></div>";
        wapfooter();
        exit;
    }
    $isfriend=isfriend($uid,$user_id);
    if($isfriend['fri']==0) {
        echo "<div class='showmag'><p>您没有关注此用户！</p><p><a href='$refer'>返回上一页</a></p></div>";
        wapfooter();
        exit;
    }
    if ($isfriend['fri']==1) {
        $db->query("DELETE FROM et_friend WHERE fid_fasong='$user_id' && fid_jieshou='$uid'");
        frinum($user_id);
        frinum($uid);

        echo "<div class='showmag'><p>解除好友成功！</p><p><a href='$refer'>返回上一页</a></p></div>";
        wapfooter();
        exit;
    }
}

//信息查询
$sql = "SELECT user_name,home_city,live_city,user_gender,user_info,user_head FROM et_users where user_id='$uid'";
$query = $db->query($sql);
$data = $db->fetch_array($query);
$username=$data['user_name'];
$home_city=$data['home_city']?$data['home_city']:"保密";
$live_city=$data['live_city']?$data['live_city']:"保密";
$user_gender=$data['user_gender']?$data['user_gender']:"保密";
$user_info=$data['user_info']?$data['user_info']:"保密";
$user_head=$data['user_head'];
$user_head=$user_head?"../attachments/head/".$user_head:"../images/noavatar.jpg";

echo "<div style='padding:2px'><p><img src='$user_head' width='96px'></p>";

if ($uid!=$user_id) {
    $isfriend=isfriend($uid,$user_id);
    if ($isfriend['fri']==1) {
        echo "<a href='index.php?op=sendmsg&uid=$uid'>发私信</a> | <a href='index.php?op=home&act=jiechu&uid=$uid&refer=".urlencode("index.php?op=home&uid=".$uid)."'>解除关注</a>";
    } else {
        echo "<a href='index.php?op=sendmsg&uid=$uid'>发私信</a> | <a href='index.php?op=home&act=guanzhu&uid=$uid&refer=".urlencode("index.php?op=home&uid=".$uid)."'>添加关注</a>";
    }
}
echo "</div><h2>".$username."在做什么...</h2><ul>";

$i=0;
$start= ($page-1)*10;
$query = $db->query("SELECT s.content_id,s.content_body,s.posttime,s.type,s.status_id,s.status_uname,s.status_type,u.user_id,u.user_name FROM et_content AS s,et_users AS u where s.user_id=u.user_id && s.user_id='$uid' order by s.content_id desc limit $start,10");
while($data = $db->fetch_array($query)) {
    $i=$i+1;
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

    if ($uid==$user_id) {
        if ($status_type=="talk") {
            echo "<li>$content_body <span class=\"stamp\">$posttime 通过".$content_type."给".$status_uname."的回复 <a href=\"index.php?act=delshare&sid=$content_id\">删除</a></span></li>";
        } else if ($status_type=="photo") {
            echo "<li>$content_body <span class=\"stamp\">$posttime 通过".$content_type."给".$status_uname."相片的回复 <a href=\"index.php?act=delshare&sid=$content_id\">删除</a></span></li>";
        } else {
            echo "<li>$content_body <span class=\"stamp\">$posttime 通过$content_type <a href=\"index.php?act=delshare&sid=$content_id\">删除</a></span></li>";
        }
    } else {
        if ($status_type=="talk") {
            echo "<li>$content_body <span class=\"stamp\">$posttime 通过".$content_type."给".$status_uname."的回复 <a href=\"index.php?op=reply&to=$content_uname&status_id=$content_id\">回复</a></span></li>";
        } else if ($status_type=="photo") {
            echo "<li>$content_body <span class=\"stamp\">$posttime 通过".$content_type."给".$status_uname."相片的回复 <a href=\"index.php?op=reply&to=$content_uname&status_id=$content_id\">回复</a></span></li>";
        } else {
            echo "<li>$content_body <span class=\"stamp\">$posttime 通过$content_type <a href=\"index.php?op=reply&to=$content_uname&status_id=$content_id\">回复</a></span></li>";
        }
    }
}
if ($i==0) echo $username."还没有发布消息";
echo "</ul>";

//分页
$query = $db->query("SELECT count(*) as count FROM et_content AS s,et_users AS u where s.user_id=u.user_id && s.user_id='$uid'");
$row = $db->fetch_array($query);
$total=$row['count'];
$pg_num=$total/10;
$pg_num=intval($pg_num);
if ($total!=$pg_num*10) $pg_num=$pg_num+1;
$np=$page+1;
$pp=$page-1;
if ($pg_num>1) {
    echo "<div class='page'>";
    if ($pp>0) echo "<a href='index.php?op=home&uid=$uid&page=$pp'>上页</a>&nbsp;";
    if ($np<=$pg_num)  echo "<a href='index.php?op=home&uid=$uid&page=$np'>下页</a>&nbsp;";
    echo "| ".$page."/".$pg_num;
    echo "</div>";
}

//资料
echo "<h2>".$username."的资料</h2>
    <p>性别：$user_gender</p>
    <p>家乡：$home_city</p>
    <p>来自：$live_city</p>
    <p>签名：$user_info</p>";
?>