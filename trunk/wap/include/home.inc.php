<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

if (!$user_id) {
    echo "<div class='showmag'><p>����û�е�¼������ִ�д˲�����</p><p><a href='index.php?op=login'>���ڵ�½</a></p></div>";
    wapfooter();
    exit;
}

$uid=$_GET['uid']?$_GET['uid']:$user_id;
$refer=$_GET['refer'];

//��ӹ�ע
if ($act =="guanzhu") {
    if(!idtoname($uid)) {
        echo "<div class='showmag'><p>���û������ڻ����Ѿ�������Աɾ����</p><p><a href='$refer'>������һҳ</a></p></div>";
        wapfooter();
        exit;
    }
    $isfriend=isfriend($uid,$user_id);
    if($isfriend['fri']==1) {
        echo "<div class='showmag'><p>���Ѿ���ע�����û���</p><p><a href='$refer'>������һҳ</a></p></div>";
        wapfooter();
        exit;
    }
    if ($isfriend['fri']==0) {
        $msg="$my[user_name] ��ע���㣬��Ҳȥ��ע���ɣ�ֻ���໥��ע�˲��ܳ�Ϊ����Ŷ��";
        $db->query("INSERT INTO et_messages  (js_id,fs_id,message_body,m_time) VALUES ('$uid','$user_id','$msg','$addtime')");
        $db->query("INSERT INTO et_friend  (fid_jieshou,fid_fasong) VALUES ('$uid','$user_id')");

        frinum($user_id);
        frinum($uid);

        echo "<div class='showmag'><p>��ע���ѳɹ���</p><p><a href='$refer'>������һҳ</a></p></div>";
        wapfooter();
        exit;
    }
}

//ɾ����ע
if ($act =="jiechu") {
    if(!idtoname($uid)) {
        echo "<div class='showmag'><p>���û������ڻ����Ѿ�������Աɾ����</p><p><a href='$refer'>������һҳ</a></p></div>";
        wapfooter();
        exit;
    }
    $isfriend=isfriend($uid,$user_id);
    if($isfriend['fri']==0) {
        echo "<div class='showmag'><p>��û�й�ע���û���</p><p><a href='$refer'>������һҳ</a></p></div>";
        wapfooter();
        exit;
    }
    if ($isfriend['fri']==1) {
        $db->query("DELETE FROM et_friend WHERE fid_fasong='$user_id' && fid_jieshou='$uid'");
        frinum($user_id);
        frinum($uid);

        echo "<div class='showmag'><p>������ѳɹ���</p><p><a href='$refer'>������һҳ</a></p></div>";
        wapfooter();
        exit;
    }
}

//��Ϣ��ѯ
$sql = "SELECT user_name,home_city,live_city,user_gender,user_info,user_head FROM et_users where user_id='$uid'";
$query = $db->query($sql);
$data = $db->fetch_array($query);
$username=$data['user_name'];
$home_city=$data['home_city']?$data['home_city']:"����";
$live_city=$data['live_city']?$data['live_city']:"����";
$user_gender=$data['user_gender']?$data['user_gender']:"����";
$user_info=$data['user_info']?$data['user_info']:"����";
$user_head=$data['user_head'];
$user_head=$user_head?"../attachments/head/".$user_head:"../images/noavatar.jpg";

echo "<div style='padding:2px'><p><img src='$user_head' width='96px'></p>";

if ($uid!=$user_id) {
    $isfriend=isfriend($uid,$user_id);
    if ($isfriend['fri']==1) {
        echo "<a href='index.php?op=sendmsg&uid=$uid'>��˽��</a> | <a href='index.php?op=home&act=jiechu&uid=$uid&refer=".urlencode("index.php?op=home&uid=".$uid)."'>�����ע</a>";
    } else {
        echo "<a href='index.php?op=sendmsg&uid=$uid'>��˽��</a> | <a href='index.php?op=home&act=guanzhu&uid=$uid&refer=".urlencode("index.php?op=home&uid=".$uid)."'>��ӹ�ע</a>";
    }
}
echo "</div><h2>".$username."����ʲô...</h2><ul>";

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
            echo "<li>$content_body <span class=\"stamp\">$posttime ͨ��".$content_type."��".$status_uname."�Ļظ� <a href=\"index.php?act=delshare&sid=$content_id\">ɾ��</a></span></li>";
        } else if ($status_type=="photo") {
            echo "<li>$content_body <span class=\"stamp\">$posttime ͨ��".$content_type."��".$status_uname."��Ƭ�Ļظ� <a href=\"index.php?act=delshare&sid=$content_id\">ɾ��</a></span></li>";
        } else {
            echo "<li>$content_body <span class=\"stamp\">$posttime ͨ��$content_type <a href=\"index.php?act=delshare&sid=$content_id\">ɾ��</a></span></li>";
        }
    } else {
        if ($status_type=="talk") {
            echo "<li>$content_body <span class=\"stamp\">$posttime ͨ��".$content_type."��".$status_uname."�Ļظ� <a href=\"index.php?op=reply&to=$content_uname&status_id=$content_id\">�ظ�</a></span></li>";
        } else if ($status_type=="photo") {
            echo "<li>$content_body <span class=\"stamp\">$posttime ͨ��".$content_type."��".$status_uname."��Ƭ�Ļظ� <a href=\"index.php?op=reply&to=$content_uname&status_id=$content_id\">�ظ�</a></span></li>";
        } else {
            echo "<li>$content_body <span class=\"stamp\">$posttime ͨ��$content_type <a href=\"index.php?op=reply&to=$content_uname&status_id=$content_id\">�ظ�</a></span></li>";
        }
    }
}
if ($i==0) echo $username."��û�з�����Ϣ";
echo "</ul>";

//��ҳ
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
    if ($pp>0) echo "<a href='index.php?op=home&uid=$uid&page=$pp'>��ҳ</a>&nbsp;";
    if ($np<=$pg_num)  echo "<a href='index.php?op=home&uid=$uid&page=$np'>��ҳ</a>&nbsp;";
    echo "| ".$page."/".$pg_num;
    echo "</div>";
}

//����
echo "<h2>".$username."������</h2>
    <p>�Ա�$user_gender</p>
    <p>���磺$home_city</p>
    <p>���ԣ�$live_city</p>
    <p>ǩ����$user_info</p>";
?>