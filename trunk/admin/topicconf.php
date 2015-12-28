<?PHP
define('is_admin_path', 'yes');
include('../common.inc.php');

if ($admin_login !="yes" || $my[isadmin]!=1)
    header("Location: admin_login.php");

if($action=="addtopic") {
    $newbody=daddslashes(trim($_POST['newbody']));
    if ($newbody) {
        $db->query("INSERT INTO et_topic (topic_body) VALUES ('$newbody')");
        @unlink(ET_ROOT."/include/cache/topic.cache.php");
        header("location: topicconf.php");
        exit;
    } else {
        echo "<script>alert('专题内容不能为空！');location.href='topicconf.php'</script>";
        exit;
    }
}

if($action=="edit") {
    $tid=$_POST['tid'];
    $tbody=daddslashes(trim($_POST['tbody']));
    if ($tbody) {
        $db->query("UPDATE et_topic SET topic_body='$tbody' where topic_id='$tid'");
        @unlink(ET_ROOT."/include/cache/topic.cache.php");
        header("location: topicconf.php");
        exit;
    } else {
        echo "<script>alert('专题内容不能为空！');location.href='topicconf.php'</script>";
        exit;
    }
}

if($act=="del") {
    $tid=$_GET['tid'];
    $sql = "select * from et_topic where topic_id='$tid'";
    $query = $db->query($sql);
    $data= $db->fetch_array($query);
    $open=$data['open'];
    if ($open==0) {
        $db->query("DELETE FROM et_topic where topic_id='$tid'");
        @unlink(ET_ROOT."/include/cache/topic.cache.php");
        echo "<script>alert('删除专题成功，点击返回！');location.href='topicconf.php'</script>";
        exit;
    } else {
        echo "<script>alert('此专题已经开启不能删除！');location.href='topicconf.php'</script>";
        exit;
    }
}

if ($act=="open") {
    $tid=$_GET['tid'];
    $db->query("UPDATE et_topic SET open='1' where topic_id='$tid'");
    @unlink(ET_ROOT."/include/cache/topic.cache.php");
    header("location: topicconf.php");
    exit;
}

if ($act=="close") {
    $tid=$_GET['tid'];
    $db->query("UPDATE et_topic SET open='0' where topic_id='$tid'");
    @unlink(ET_ROOT."/include/cache/topic.cache.php");
    header("location: topicconf.php");
    exit;
}


$sql = "select * from et_topic order by topic_id";
$query = $db->query($sql);
while ($data= $db->fetch_array($query)) {
    $topic_id=$data['topic_id'];
    $topic_body=$data['topic_body'];
    $open=$data['open']==1?"是":"否";

    $topic[] = array("topic_id"=>$topic_id,"topic_body"=>$topic_body,"open"=>$open);
}

include($template->getfile('topicconf.htm'));
?>