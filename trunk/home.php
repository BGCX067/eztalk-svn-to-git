<?PHP
include('common.inc.php');

$uid=isset($_GET['uid'])?$_GET['uid']:$my[user_id];
$page=isset($_GET['page'])?intval($_GET['page']):1;
$hm=$_GET['hm'];
$tplhm="template \"hm_".$hm.".htm\"";

if (!$uid) {
	header("Location: $webaddr/op/login");
}

$sql = "select * from et_users where user_id='$uid'";
$query = $db->query($sql);
$user= $db->fetch_array($query);
$tem1=explode(" ",$user['home_city']);
$tem2=explode(" ",$user['live_city']);
$tem3=explode(" ",$user['msn']);
$user['user_head']=$user['user_head']?"$webaddr/attachments/head/".$user['user_head']:"$webaddr/images/noavatar.jpg";
$user['old']=date(Y)-get_substr($user['birthday'],0,4);
if($user['home_city']=="ѡ��ʡ�� ѡ�����" || $user['home_city']=="" || $user['home_city']==" ") $user['home_city']="";
else $user['home_city']="<a href='$webaddr/op?op=finder&&sname=&act=search&homesf=".$tem1[0]."&homecity=".$tem1[1]."'>".$user['home_city']."</a>";

if($user['live_city']=="ѡ��ʡ�� ѡ�����" || $user['live_city']=="" || $user['live_city']==" ") $user['live_city']="";
else $user['live_city']="<a href='$webaddr/op?op=finder&sname=&act=search&livesf=".$tem2[0]."&livecity=".$tem2[1]."'>".$user['live_city']."</a>";

if (count($tem3)==2) {
    $user['msn']=$tem3[0];
    $user['msnyz']=$tem3[1];
} else $user['msnyz']="";


if ($user['userlock']==1){
    echo "<script>alert(\"��ã�����ʵ��û� $user[user_name] ������Ա���Σ�\");window.location.href='$webaddr/index';</script>";
    exit;
}

if (!$user['user_name']){
    echo "<script>alert(\"��ã�����ʵ��û��ռ䲻���ڣ�\");window.location.href='$webaddr/index';</script>";
    exit;
}

if ($user['user_id']!=$my['user_id'] && $user['isclose']=="��") {
    echo "<script>alert(\"��ã������ڷ��ʵĿռ��Ѿ����ռ����˹ر��ˣ���\");window.location.href='$webaddr/index';</script>";
    exit;
}

if ($user['user_id']==$my['user_id']) {
    $query_ly = $db->query("select count(*) as count from et_messages where js_id='$user[user_id]' && isread='0'");
    $row = $db->fetch_array($query_ly);
    $user['ly'] = $row['count'];
}

//�����ж�
if ($user['user_id']!=$my['user_id']) {
    $isfriend=isfriend($user['user_id'],$my[user_id]);
}

//�ҵĺ���--��ѯ����id
if ($user['friend_num']!=0) {
    $sql_f = "select u.user_id,u.user_name,u.user_head from et_friend as f1,et_friend as f2,et_users as u where f1.fid_jieshou=f2.fid_fasong && f2.fid_jieshou=f1.fid_fasong && f1.fid_jieshou='$uid' && f2.fid_jieshou=u.user_id order by f1.fri_id desc limit 42";
    $query_f = $db->query($sql_f);
    while ($data=$db->fetch_array($query_f)){
        $usid=$data['user_id'];
        $usname=$data['user_name'];
        $ushead=$data['user_head']?"$webaddr/attachments/head/".$data['user_head']:"$webaddr/images/noavatar.jpg";

        $myfri[] = array("usid"=>$usid,"usname"=>$usname,"ushead"=>$ushead);
    }
}

//�ظ�����
$toname=$_GET['to'];
if ($toname) {
    $toid=nametoid($toname);
    $status_id =$_GET["status_id"];
    $statustype =$_GET["status_type"]?$_GET["status_type"]:"talk";
    if ($statustype=="talk") $sql = "select content_id from et_content where content_id='$status_id' && user_id='$toid'";
    elseif ($statustype=="photo") $sql = "select pt_id from et_photos where pt_id='$status_id' && user_id='$toid'";
    $query = $db->query($sql);
    $data = $db->fetch_array($query);
    if (!$data) $toname="";
}

//ר������
$topicid=$_GET['topicid'];
if ($topicid) {
    $sql = "select topic_body from et_topic where topic_id='$topicid' && open=1";
	$query = $db->query($sql);
	$data = $db->fetch_array($query);
    $topicbody=$data['topic_body'];
}

//�Ӻ���
if ($act =="friendadd") {
    tologin();
    if(!idtoname($user[user_id])) {
        if ($refer) echo "<script>alert(\"��ʾ�����û������ڻ����Ѿ�������Աɾ����\");location.href='$refer';</script>";
        else echo "<script>alert(\"��ʾ�����û������ڻ����Ѿ�������Աɾ����\");location.href='$webaddr/home';</script>";
        exit;
    }
    $isfriend=isfriend($user[user_id],$my[user_id]);
    if($isfriend['fri']==1) {
        echo "<script>alert(\"��ʾ�����Ѿ���ע�����û���\");location.href='$webaddr/home/u.$user[user_id]';</script>";
        exit;
    }
    if ($isfriend['fri']==0) {
        $msg="$my[user_name] ��ע���㣬��Ҳȥ��ע���ɣ�ֻ���໥��ע�˲��ܳ�Ϊ����Ŷ��";
        $db->query("INSERT INTO et_messages  (js_id,fs_id,message_body,m_time) VALUES ('$user[user_id]','$my[user_id]','$msg','$addtime')");
        $db->query("INSERT INTO et_friend  (fid_jieshou,fid_fasong) VALUES ('$user[user_id]','$my[user_id]')");

        frinum($my[user_id]);
        frinum($user[user_id]);

        fsock($user[user_id],"��СT���ѡ� ".$my[user_name]."��ע���㣬��ȥ����TA�ɣ�TA�ĵ�ַ:".$webaddr."/home/u.".$my[user_id]);

        if ($refer) header("Location: ".urldecode($refer)."&tip=21");
        else header("Location: $webaddr/home/u.$user[user_id]&tip=21");
        exit;
    }
}

//ɾ������ home
if ($act =="fridel") {
    tologin();
    if(!idtoname($user[user_id])) {
        echo "<script>alert(\"��ʾ�����û������ڻ����Ѿ�������Աɾ����\");location.href='$webaddr/home';</script>";
        exit;
    }
    $isfriend=isfriend($user[user_id],$my[user_id]);
    if($isfriend['fri']==0) {
        echo "<script>alert(\"��ʾ����û�й�ע���û���\");location.href='$webaddr/home';</script>";
        exit;
    }
    if ($isfriend['fri']==1) {
        $db->query("DELETE FROM et_friend WHERE fid_fasong='$my[user_id]' && fid_jieshou='$user[user_id]'");
        frinum($my[user_id]);
        frinum($user[user_id]);

        fsock($user[user_id],"��СT���ѡ� ".$my[user_name]."����˶���Ĺ�ע����ȥ����TA�ɣ�TA�ĵ�ַ:".$webaddr."/home/u.".$my[user_id]);
        header("Location: $webaddr/home/u.$user[user_id]&tip=23");
        exit;
    }
}

//ɾ������ friends
if ($act =="frienddel") {
    tologin();
    if(!idtoname($user[user_id])) {
        echo "���û������ڻ����Ѿ�������Աɾ����";
        exit;
    }
    $isfriend=isfriend($user[user_id],$my[user_id]);
    if($isfriend['fri']==0) {
        echo "��û�й�ע���û���";
        exit;
    }
    if ($isfriend['fri']==1) {
        $db->query("DELETE FROM et_friend WHERE fid_fasong='$my[user_id]' && fid_jieshou='$user[user_id]'");
        frinum($my[user_id]);
        frinum($user[user_id]);

        fsock($user[user_id],"��СT���ѡ� ".$my[user_name]."����˶���Ĺ�ע����ȥ����TA�ɣ�TA�ĵ�ַ:".$webaddr."/home/u.".$my[user_id]);
        echo "success";
        exit;
    }
}

//������ ���� ����
if ($action =="msgsend") {
    tologin();
    $content = daddslashes(trim($_POST["content"]));
    $toid=$_POST["toid"];
    $status_id = $_POST["status_id"];
    $status_type = $_POST["status_type"];
    $topid = $_POST["topid"]?$_POST["topid"]:0;
    $toname=idtoname($toid);

    if (!empty($content)) {
        $content=replace($content); //�������
        if ($toid && $status_id) {
           $content="@<a href=\"$webaddr/home/u.$toid\">$toname</a> $content";

           $db->query("INSERT INTO et_content (user_id,topicid,content_body,posttime,status_id,status_uid,status_uname,status_type) VALUES ('$my[user_id]','$topid','$content','$addtime','$status_id','$toid','$toname','$status_type')");
           fsock($toid,"��СT���ѡ� ".$my[user_name]."�ظ������TALK���鿴��ַ:".$webaddr."/home/replies");
        }else {
            $db->query("INSERT INTO et_content (user_id,topicid,content_body,posttime) VALUES ('$my[user_id]','$topid','$content','$addtime')");
        }
        $db->query("UPDATE et_users SET msg_num='".($my[msg_num]+1)."' where user_id='$my[user_id]'");
        header("location: $webaddr/home");
        exit;
    } else {
        header("location: $webaddr/home?tip=25");
        exit;
    }
}

if ($hm) {
    include('source/hm_'.$hm.'.inc.php');
    exit;
}

//ģ���Foot
$web_name3=$user[user_name];
include($template->getfile('home.htm'));
?>