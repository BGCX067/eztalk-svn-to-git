<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

if (!$user_id) {
    echo "<div class='showmag'><p>����û�е�¼������ִ�д˲�����</p><p><a href='index.php?op=login'>���ڵ�½</a></p></div>";
    wapfooter();
    exit;
}

$fop=$_GET['fop']?$_GET['fop']:"fri";

//ɾ����ʾ
if($act=="delfri") {
    $uid=$_GET['uid'];
    $uname=idtoname($uid);
    if(!$uname) {
        echo "<div class='showmag'><p>���û������ڻ����Ѿ�������Աɾ����</p><p><a href='index.php?op=friends'>�����ҵĹ�ע</a></p></div>";
        wapfooter();
        exit;
    }
    $isfriend=isfriend($uid,$user_id);
    if($isfriend['fri']==0) {
        echo "<div class='showmag'><p>��û�й�ע���û���</p><p><a href='index.php?op=friends'>�����ҵĹ�ע</a></p></div>";
        wapfooter();
        exit;
    }
    if ($isfriend['fri']==1) {
        echo "<div class='showmag'><p>�Ƿ�ȷ�Ͻ����".$uname."�Ĺ�ע��</p><p><a href='index.php?op=friends&act=delfriok&uid=$uid'>ȷ��</a> <a href='index.php?op=friends'>ȡ��</a></p></div>";
        wapfooter();
        exit;
    }
}

//ɾ��ȷ��
if ($act=="delfriok"){
    $uid=$_GET['uid'];
    $uid=$_GET['uid'];
    $uname=idtoname($uid);
    if(!$uname) {
        echo "<div class='showmag'><p>���û������ڻ����Ѿ�������Աɾ����</p><p><a href='index.php?op=friends'>�����ҵĹ�ע</a></p></div>";
        wapfooter();
        exit;
    }
    $isfriend=isfriend($uid,$user_id);
    if($isfriend['fri']==0) {
        echo "<div class='showmag'><p>��û�й�ע���û���</p><p><a href='index.php?op=friends'>�����ҵĹ�ע</a></p></div>";
        wapfooter();
        exit;
    }
    if($isfriend['fri']==1) {
        $db->query("DELETE FROM et_friend WHERE fid_fasong='$user_id' && fid_jieshou='$uid'");
        frinum($user_id);
        frinum($uid);
        echo "<div class='showmag'><p>�����ע�ɹ���</p><p><a href='index.php?op=friends'>�����ҵĹ�ע</a></p></div>";
        wapfooter();
        exit;
    }
}

//����
if ($fop=="fri") echo "<h2>�ҹ�ע | <a href='index.php?op=friends&fop=fol'>��ע��</a> | <a href='index.php?op=friends&fop=all'>����</a></h2>";
else if ($fop=="fol") echo "<h2><a href='index.php?op=friends&fop=fri'>�ҹ�ע</a> | ��ע�� | <a href='index.php?op=friends&fop=all'>����</a></h2>";
else if ($fop=="all") echo "<h2><a href='index.php?op=friends&fop=fri'>�ҹ�ע</a> | <a href='index.php?op=friends&fop=fol'>��ע��</a> | ����</h2>";

if ($fop=="fri") {
    echo "<ul>";
    $start= ($page-1)*10;
    $sql_f = "select u.user_id,u.user_name,u.user_info from et_friend as f,et_users as u where f.fid_fasong='$user_id' && f.fid_jieshou=u.user_id order by f.fri_id desc limit $start,10";
    $query_f = $db->query($sql_f);
    while ($data=$db->fetch_array($query_f)){
        $usid=$data['user_id'];
        $usname=$data['user_name'];
        $usinfo=$data['user_info'];

        echo "<li><a href='index.php?op=home&uid=$usid'>$usname</a> $usinfo <span class=\"stamp\"><a href=\"index.php?op=sendmsg&uid=$usid\">˽��</a> <a href=\"index.php?op=friends&act=delfri&uid=$usid\">ɾ��</a></span></li>";
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
        if ($pp>0) echo "<a href='index.php?op=friends&fop=fri&page=$pp'>��ҳ</a>&nbsp;";
        if ($np<=$pg_num) echo "<a href='index.php?op=friends&fop=fri&page=$np'>��ҳ</a>&nbsp;";
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

        echo "<li><a href='index.php?op=home&uid=$usid'>$usname</a> $usinfo <span class=\"stamp\"><a href=\"index.php?op=sendmsg&uid=$usid\">˽��</a></span></li>";
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
        if ($pp>0) echo "<a href='index.php?op=friends&fop=fol&page=$pp'>��ҳ</a>&nbsp;";
        if ($np<=$pg_num) echo "<a href='index.php?op=friends&fop=fol&page=$np'>��ҳ</a>&nbsp;";
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

        echo "<li><a href='index.php?op=home&uid=$usid'>$usname</a> $usinfo <span class=\"stamp\"><a href=\"index.php?op=sendmsg&uid=$usid\">˽��</a></span></li>";
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
        if ($pp>0) echo "<a href='index.php?op=friends&fop=all&page=$pp'>��ҳ</a>&nbsp;";
        if ($np<=$pg_num) echo "<a href='index.php?op=friends&fop=all&page=$np'>��ҳ</a>&nbsp;";
        echo "| ".$page."/".$pg_num;
        echo "</div>";
    }
}
?>