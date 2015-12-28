<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

if (!$user_id) {
    echo "<div class='showmag'><p>您还没有登录，不能执行此操作！</p><p><a href='index.php?op=login'>现在登陆</a></p></div>";
    wapfooter();
    exit;
}

$fop=$_GET['fop']?$_GET['fop']:"fri";

//删除提示
if($act=="delfri") {
    $uid=$_GET['uid'];
    $uname=idtoname($uid);
    if(!$uname) {
        echo "<div class='showmag'><p>该用户不存在或者已经被管理员删除！</p><p><a href='index.php?op=friends'>返回我的关注</a></p></div>";
        wapfooter();
        exit;
    }
    $isfriend=isfriend($uid,$user_id);
    if($isfriend['fri']==0) {
        echo "<div class='showmag'><p>您没有关注此用户！</p><p><a href='index.php?op=friends'>返回我的关注</a></p></div>";
        wapfooter();
        exit;
    }
    if ($isfriend['fri']==1) {
        echo "<div class='showmag'><p>是否确认解除对".$uname."的关注？</p><p><a href='index.php?op=friends&act=delfriok&uid=$uid'>确认</a> <a href='index.php?op=friends'>取消</a></p></div>";
        wapfooter();
        exit;
    }
}

//删除确认
if ($act=="delfriok"){
    $uid=$_GET['uid'];
    $uid=$_GET['uid'];
    $uname=idtoname($uid);
    if(!$uname) {
        echo "<div class='showmag'><p>该用户不存在或者已经被管理员删除！</p><p><a href='index.php?op=friends'>返回我的关注</a></p></div>";
        wapfooter();
        exit;
    }
    $isfriend=isfriend($uid,$user_id);
    if($isfriend['fri']==0) {
        echo "<div class='showmag'><p>您没有关注此用户！</p><p><a href='index.php?op=friends'>返回我的关注</a></p></div>";
        wapfooter();
        exit;
    }
    if($isfriend['fri']==1) {
        $db->query("DELETE FROM et_friend WHERE fid_fasong='$user_id' && fid_jieshou='$uid'");
        frinum($user_id);
        frinum($uid);
        echo "<div class='showmag'><p>解除关注成功！</p><p><a href='index.php?op=friends'>返回我的关注</a></p></div>";
        wapfooter();
        exit;
    }
}

//导航
if ($fop=="fri") echo "<h2>我关注 | <a href='index.php?op=friends&fop=fol'>关注我</a> | <a href='index.php?op=friends&fop=all'>好友</a></h2>";
else if ($fop=="fol") echo "<h2><a href='index.php?op=friends&fop=fri'>我关注</a> | 关注我 | <a href='index.php?op=friends&fop=all'>好友</a></h2>";
else if ($fop=="all") echo "<h2><a href='index.php?op=friends&fop=fri'>我关注</a> | <a href='index.php?op=friends&fop=fol'>关注我</a> | 好友</h2>";

if ($fop=="fri") {
    echo "<ul>";
    $start= ($page-1)*10;
    $sql_f = "select u.user_id,u.user_name,u.user_info from et_friend as f,et_users as u where f.fid_fasong='$user_id' && f.fid_jieshou=u.user_id order by f.fri_id desc limit $start,10";
    $query_f = $db->query($sql_f);
    while ($data=$db->fetch_array($query_f)){
        $usid=$data['user_id'];
        $usname=$data['user_name'];
        $usinfo=$data['user_info'];

        echo "<li><a href='index.php?op=home&uid=$usid'>$usname</a> $usinfo <span class=\"stamp\"><a href=\"index.php?op=sendmsg&uid=$usid\">私信</a> <a href=\"index.php?op=friends&act=delfri&uid=$usid\">删除</a></span></li>";
    }
    echo "</ul>";

    $sql ="select count(*) as count from et_friend as f,et_users as u where f.fid_fasong='$user_id' && f.fid_jieshou=u.user_id";
    $query = $db->query($sql);
    $row = $db->fetch_array($query);
    $total=$row['count'];
    $pg_num=$total/10;
    $pg_num=intval($pg_num);
    if ($total!=$pg_num*10) $pg_num=$pg_num+1;
    $np=$page+1;
    $pp=$page-1;
    if ($pg_num>1) {
        echo "<div class='page'>";
        if ($pp>0) echo "<a href='index.php?op=friends&fop=fri&page=$pp'>上页</a>&nbsp;";
        if ($np<=$pg_num) echo "<a href='index.php?op=friends&fop=fri&page=$np'>下页</a>&nbsp;";
        echo "| ".$page."/".$pg_num;
        echo "</div>";
    }
} else if ($fop=="fol"){
    echo "<ul>";
    $start= ($page-1)*10;
    $sql_f = "select u.user_id,u.user_name,u.user_info from et_friend as f,et_users as u where f.fid_jieshou='$user_id' && f.fid_fasong=u.user_id order by f.fri_id desc limit $start,10";
    $query_f = $db->query($sql_f);
    while ($data=$db->fetch_array($query_f)){
        $usid=$data['user_id'];
        $usname=$data['user_name'];
        $usinfo=$data['user_info'];

        echo "<li><a href='index.php?op=home&uid=$usid'>$usname</a> $usinfo <span class=\"stamp\"><a href=\"index.php?op=sendmsg&uid=$usid\">私信</a></span></li>";
    }
    echo "</ul>";

    $sql ="select count(*) as count from et_friend as f,et_users as u where f.fid_jieshou='$user_id' && f.fid_fasong=u.user_id";
    $query = $db->query($sql);
    $row = $db->fetch_array($query);
    $total=$row['count'];
    $pg_num=$total/10;
    $pg_num=intval($pg_num);
    if ($total!=$pg_num*10) $pg_num=$pg_num+1;
    $np=$page+1;
    $pp=$page-1;
    if ($pg_num>1) {
        echo "<div class='page'>";
        if ($pp>0) echo "<a href='index.php?op=friends&fop=fol&page=$pp'>上页</a>&nbsp;";
        if ($np<=$pg_num) echo "<a href='index.php?op=friends&fop=fol&page=$np'>下页</a>&nbsp;";
        echo "| ".$page."/".$pg_num;
        echo "</div>";
    }
} else if ($fop=="all") {
    echo "<ul>";
    $start= ($page-1)*10;
    $sql_f = "select f1.fid_jieshou,f2.fid_jieshou,u.user_id,u.user_name,u.user_info from et_friend as f1,et_friend as f2,et_users as u where f1.fid_jieshou=f2.fid_fasong && f2.fid_jieshou=f1.fid_fasong && f1.fid_jieshou='$user_id' && f2.fid_jieshou=u.user_id order by f1.fri_id desc limit $start,10";
    $query_f = $db->query($sql_f);
    while ($data=$db->fetch_array($query_f)){
        $usid=$data['user_id'];
        $usname=$data['user_name'];
        $usinfo=$data['user_info'];

        echo "<li><a href='index.php?op=home&uid=$usid'>$usname</a> $usinfo <span class=\"stamp\"><a href=\"index.php?op=sendmsg&uid=$usid\">私信</a></span></li>";
    }
    echo "</ul>";

    $sql ="select count(*) as count from et_friend as f1,et_friend as f2 where f1.fid_jieshou=f2.fid_fasong && f2.fid_jieshou=f1.fid_fasong && f1.fid_jieshou='$user_id'";
    $query = $db->query($sql);
    $row = $db->fetch_array($query);
    $total=$row['count'];
    $pg_num=$total/10;
    $pg_num=intval($pg_num);
    if ($total!=$pg_num*10) $pg_num=$pg_num+1;
    $np=$page+1;
    $pp=$page-1;
    if ($pg_num>1) {
        echo "<div class='page'>";
        if ($pp>0) echo "<a href='index.php?op=friends&fop=all&page=$pp'>上页</a>&nbsp;";
        if ($np<=$pg_num) echo "<a href='index.php?op=friends&fop=all&page=$np'>下页</a>&nbsp;";
        echo "| ".$page."/".$pg_num;
        echo "</div>";
    }
}
?>