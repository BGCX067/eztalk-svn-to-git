<?PHP
if(!defined('IN_ET')) {
	exit('Access Denied');
}

$uid=isset($_GET['uid'])?$_GET['uid']:$my[user_id];
$page=isset($_GET['page'])?intval($_GET['page']):1;
$act=$act?$act:"fri";
$start=($page-1)*20;

$sql = "select user_name,theme_bgcolor,theme_pictype,theme_text,theme_link,theme_sidebar,theme_sidebox,theme_bgurl from et_users where user_id='$uid'";
$query = $db->query($sql);
$data= $db->fetch_array($query);
$uname=$data['user_name'];
$user=array("user_id"=>$uid,"theme_bgcolor"=>$data['theme_bgcolor'],"theme_pictype"=>$data['theme_pictype'],"theme_text"=>$data['theme_text'],"theme_link"=>$data['theme_link'],"theme_sidebar"=>$data['theme_sidebar'],"theme_sidebox"=>$data['theme_sidebox'],"theme_bgurl"=>$data['theme_bgurl']);

if ($act=="fri") {
    //好友
    $sql_f = "select u.user_id,u.user_name,u.user_head from et_friend as f,et_users as u where f.fid_fasong='$uid' && f.fid_jieshou=u.user_id order by f.fri_id desc limit $start,20";
    $query_f = $db->query($sql_f);
    while ($data=$db->fetch_array($query_f)){
        $usid=$data['user_id'];
        $usname=$data['user_name'];
        $ushead=$data['user_head']?"$webaddr/attachments/head/".$data['user_head']:"$webaddr/images/noavatar.jpg";

        $myfri[] = array("usid"=>$usid,"usname"=>$usname,"ushead"=>$ushead);
    }

    $sql ="select count(*) as count from et_friend as f,et_users as u where f.fid_fasong='$uid' && f.fid_jieshou=u.user_id";
    $query = $db->query($sql);
    $row = $db->fetch_array($query);
    $total=$row['count'];
    $pg_num=$total/20;
    $pg_num=intval($pg_num);
    if ($total!=$pg_num*20)
        $pg_num=$pg_num+1;
    $np=$page+1;
    $pp=$page-1;
    if ($pg_num>8) {
        if ($pg_num-$page<=6)
            $page_from=($page-1)==0?1:$pg_num-7;
        else
            $page_from=($page-1)==0?1:$page-1;
        $page_end=($page_from+7)>$pg_num?$pg_num:$page_from+7;
    }else{
        $page_from=1;
        $page_end=$pg_num;
    }
}

if ($act=="fol") {
    //关注
    $sql_f = "select u.user_id,u.user_name,u.user_head,if (f2.fid_jieshou is null,'0',f2.fid_jieshou)fri from et_users as u,et_friend as f1 left join et_friend as f2 on f1.fid_jieshou=f2.fid_fasong && f2.fid_jieshou=f1.fid_fasong where f1.fid_jieshou='$uid' && f1.fid_fasong=u.user_id order by f1.fri_id desc limit $start,20;";
    $query_f = $db->query($sql_f);
    while ($data=$db->fetch_array($query_f)){
        $usid=$data['user_id'];
        $usname=$data['user_name'];
        $ushead=$data['user_head']?"$webaddr/attachments/head/".$data['user_head']:"$webaddr/images/noavatar.jpg";
        $isf=$data['fri']==0?0:1;

        $myfri[] = array("usid"=>$usid,"usname"=>$usname,"ushead"=>$ushead,"isf"=>$isf);
    }

    $sql ="select count(*) as count from et_friend as f,et_users as u where f.fid_jieshou='$uid' && f.fid_fasong=u.user_id";
    $query = $db->query($sql);
    $row = $db->fetch_array($query);
    $total=$row['count'];
    $pg_num=$total/20;
    $pg_num=intval($pg_num);
    if ($total!=$pg_num*20)
        $pg_num=$pg_num+1;
    $np=$page+1;
    $pp=$page-1;
    if ($pg_num>8) {
        if ($pg_num-$page<=6)
            $page_from=($page-1)==0?1:$pg_num-7;
        else
            $page_from=($page-1)==0?1:$page-1;
        $page_end=($page_from+7)>$pg_num?$pg_num:$page_from+7;
    }else{
        $page_from=1;
        $page_end=$pg_num;
    }
}

if ($act=="allfri") {
    //互相关注
    $sql_f = "select u.user_id,u.user_name,u.user_head from et_friend as f1,et_friend as f2,et_users as u where f1.fid_jieshou=f2.fid_fasong && f2.fid_jieshou=f1.fid_fasong && f1.fid_jieshou='$uid' && f2.fid_jieshou=u.user_id order by f1.fri_id desc limit $start,20";
    $query_f = $db->query($sql_f);
    while ($data=$db->fetch_array($query_f)){
        $usid=$data['user_id'];
        $usname=$data['user_name'];
        $ushead=$data['user_head']?"$webaddr/attachments/head/".$data['user_head']:"$webaddr/images/noavatar.jpg";

        $myfri[] = array("usid"=>$usid,"usname"=>$usname,"ushead"=>$ushead);
    }

    $sql ="select count(*) as count from et_friend as f1,et_friend as f2 where f1.fid_jieshou=f2.fid_fasong && f2.fid_jieshou=f1.fid_fasong && f1.fid_jieshou='$uid'";
    $query = $db->query($sql);
    $row = $db->fetch_array($query);
    $total=$row['count'];
    $pg_num=$total/20;
    $pg_num=intval($pg_num);
    if ($total!=$pg_num*20)
        $pg_num=$pg_num+1;
    $np=$page+1;
    $pp=$page-1;
    if ($pg_num>8) {
        if ($pg_num-$page<=6)
            $page_from=($page-1)==0?1:$pg_num-7;
        else
            $page_from=($page-1)==0?1:$page-1;
        $page_end=($page_from+7)>$pg_num?$pg_num:$page_from+7;
    }else{
        $page_from=1;
        $page_end=$pg_num;
    }
    if($my[friend_num]!=$total && $uid==$my[user_id]) {
        $db->query("update et_users set friend_num='$total' where user_id='$my[user_id]'");
    }
}

//模板和Foot
$web_name3=$uname."好友";
$sqlnum=$db->querynum;
$mtime = explode(' ', microtime());
$loadtime=$mtime[1] + $mtime[0] - $starttime;
include($template->getfile('op_friends.htm'));
?>
