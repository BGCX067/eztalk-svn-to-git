<?PHP
if(!defined('IN_ET')) {
	exit('Access Denied');
}

tologin();

if ($action=="upload") {
    $syshead= daddslashes($_POST["syshead"]);

    $sysphoto = array("$webaddr/attachments/head/syshead/01.jpg","$webaddr/attachments/head/syshead/02.jpg","$webaddr/attachments/head/syshead/03.jpg","$webaddr/attachments/head/syshead/04.jpg","$webaddr/attachments/head/syshead/05.jpg","$webaddr/attachments/head/syshead/06.jpg","$webaddr/attachments/head/syshead/07.jpg","$webaddr/attachments/head/syshead/08.jpg","$webaddr/attachments/head/syshead/09.jpg","$webaddr/attachments/head/syshead/10.jpg");

    if ($_FILES['picture']['name']) {
        $refer=$webaddr."/op/setting";
        include(ET_ROOT."/include/uploadpic.func.php");
        $upname=UploadImage("picture",1,96,96,ET_ROOT."/attachments/head/",ET_ROOT."/attachments/head/",$my['user_id']);
        $db->query("UPDATE et_users SET user_head = '$upname' WHERE  user_id='$my[user_id]'");
    }

    if (in_array($syshead, $sysphoto) && !$_FILES['picture']['name']) {
        $user_syshead=get_substr($syshead,-14,0);
        $db->query("UPDATE et_users  SET user_head = '$user_syshead' WHERE  user_id='$my[user_id]'");
    }

    echo "<script>location.href='$webaddr/op/setting&tip=30'</script>";
    exit;
}

if ($action=="setting") {
    $gender= $_POST["gender"];
    $musicaddr= daddslashes(trim($_POST["musicaddr"]));
    $homesf= $_POST["homesf"];
    $homecity= $_POST["homecity"];
    $livesf= $_POST["livesf"];
    $livecity= $_POST["livecity"];
    $homeprovince="$homesf $homecity";
    $liveprovince="$livesf $livecity";
    $birthyear= $_POST["birthyear"];
    $birthmonth= $_POST["birthmonth"];
    $birthday= $_POST["birthday"];
    if ($birthyear && $birthmonth && $birthday) $birth=$birthyear."-".$birthmonth."-".$birthday;
    else $birth="";
    $info= replace(daddslashes(trim($_POST["info"])));
    $close= $_POST["isclose"];
    $sendmsg= $_POST["issendmsg"];

    if ($homeprovince!=$my[home_city] && $homesf!="选择省份" && $homecity!="选择城市"){
        $db->query("UPDATE et_users  SET home_city = '$homeprovince' WHERE  user_id='$my[user_id]'");
    }

    if ($liveprovince!=$my[home_city] && $livesf!="选择省份" && $livecity!="选择城市"){
        $db->query("UPDATE et_users  SET live_city = '$liveprovince' WHERE  user_id='$my[user_id]'");
    }

    if ($birth!=$my[birthday] && $birth){
        $db->query("UPDATE et_users  SET birthday = '$birth' WHERE  user_id='$my[user_id]'");
    }

    if ($gender!=$my[user_gender]){
        $db->query("UPDATE et_users  SET user_gender = '$gender' WHERE  user_id='$my[user_id]'");
    }

    if ($info!=$my[user_info]){
        $db->query("UPDATE et_users  SET user_info = '$info' WHERE  user_id='$my[user_id]'");
    }

    if ($close!=$my[isclose]){
        $db->query("UPDATE et_users  SET isclose = '$close' WHERE  user_id='$my[user_id]'");
    }

    if ($sendmsg!=$my[issendmsg]){
        $db->query("UPDATE et_users  SET issendmsg = '$sendmsg' WHERE  user_id='$my[user_id]'");
    }

    if ($musicaddr!=$my[musicaddr]){
        $db->query("UPDATE et_users  SET musicaddr = '$musicaddr' WHERE  user_id='$my[user_id]'");
    }

    header("location:$webaddr/op/setting&tip=3");
    exit;
}

//模板和Foot
$web_name3="基本信息设置";
$sqlnum=$db->querynum;
$mtime = explode(' ', microtime());
$loadtime=$mtime[1] + $mtime[0] - $starttime;
include($template->getfile('op_setting.htm'));
?>