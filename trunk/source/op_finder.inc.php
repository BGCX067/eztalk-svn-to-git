<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

$page=$_GET['page']?intval($_GET['page']):1;
$oc=$_GET['oc']?$_GET['oc']:"search";

//��������
if ($action=="invite") {
    tologin();
    $send_body=trim($_POST['textarea']);
    $s_mail=trim($_POST['email']);
    $email_message_title="$my[user_name] ����������EasyTalk";
    $send_body="����!
    $send_body
    �ǵ��������ҵĿռ䣺
    $webaddr/home/u.$my[user_id]

    EasyTalk,��һ�����㲩�ͣ������ԣ�
    ������ʱ��ط�����Ϣ��������������
    ����ʱʱ�̹̿�ע���ѵ�����
    ����ͨ���ֻ�����ҳ��MSN��QQ��Gtalk����......

    ��ĺ��� $my[user_name]";

    if ($s_mail) {
        include("include/mail.class.php");
        sendmail($s_mail,$email_message_title,$send_body);

        header("Location: $webaddr/op/finder&oc=invite&tip=23");
        exit;
    } else {
        header("Location: $webaddr/op/finder&oc=invite&tip=26");
        exit;
    }
}

//������ѯ
if ($act=="search")  {
    $sname=daddslashes($_GET['sname']);
    $gender= $_GET["gender"];
    if ($_GET["homesf"] && $_GET["homecity"]) $homepro=$_GET["homesf"]." ".$_GET["homecity"];
    else $homepro="";

    if ($_GET["livesf"] && $_GET["livecity"]) $livepro=$_GET["livesf"]." ".$_GET["livecity"];
    else $livepro="";

    if ($gender) $ext1=" && user_gender='$gender' ";
    if ($homepro) $ext2=" && home_city='$homepro' ";
    if ($livepro) $ext3=" && live_city='$livepro' ";
    $ext=$ext1.$ext2.$ext3;

    if (!$sname && !$gender && !$homepro && !$livepro) {
        $i=-1;
    } else {
        $i=0;
        $start= ($page-1)*10;
        $query = $db->query("SELECT user_id,user_name,user_head,user_gender,home_city,live_city FROM et_users where user_name like '%$sname%' $ext order by user_id limit $start,10");
        while($data = $db->fetch_array($query)) {
            $i=$i+1;
            $f_uid=$data['user_id'];
            $f_uname=$data['user_name'];
            $f_ugender=$data['user_gender']?$data['user_gender']:"δ��д";
            $f_homepro=$data['home_city']?$data['home_city']:"δ��д";
            $f_livepro=$data['live_city']?$data['live_city']:"δ��д";
            $f_uhead=$data['user_head']?"$webaddr/attachments/head/".$data['user_head']:"$webaddr/images/noavatar.jpg";

            $find[] = array("f_uid"=>$f_uid,"f_uname"=>$f_uname,"f_ugender"=>$f_ugender,"f_homepro"=>$f_homepro,"f_livepro"=>$f_livepro,"f_uhead"=>$f_uhead);
        }

        $query = $db->query("select count(*) as count from et_users where user_name like '%$sname%' $ext");
        $row = $db->fetch_array($query);
        $total=$row['count'];
        $pg_num=$total/10;
        $pg_num=intval($pg_num);
        if ($total!=$pg_num*10)
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
}

//ģ���Foot
$web_name3=$oc=="search"?"������":"����";
$sqlnum=$db->querynum;
$mtime = explode(' ', microtime());
$loadtime=$mtime[1] + $mtime[0] - $starttime;
include($template->getfile('op_finder.htm'));
?>