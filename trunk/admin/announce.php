<?PHP
define('is_admin_path', 'yes');
include('../common.inc.php');

if ($admin_login !="yes" || $my[isadmin]!=1)
    header("Location: admin_login.php");

if ($action=="add") {
    $annbody =daddslashes(trim($_POST["annbody"]));
    if ($annbody ==""){
        echo "<script>alert('添加失败，请填写公告内容！');location.href='announce.php'</script>";
        exit;
    }else{
        $db->query("INSERT INTO et_announ (announ_body,announ_time) VALUES ('$annbody','$addtime')");
    }
    @unlink(ET_ROOT."/include/cache/userannounce.cache.php");
    echo "<script>alert('公告添加成功！');location.href='announce.php'</script>";
    exit;
}

if ($action=="edit"){
    $addid = $_POST["addid"];
    $new_body =daddslashes(trim($_POST["new_body"]));
    if ($new_body ==""){
        echo "<script>alert('添加失败，请填写公告内容！');location.href='announce.php'</script>";
        exit;
    }else{
        @unlink(ET_ROOT."/include/cache/userannounce.cache.php");
        $db->query("UPDATE et_announ SET announ_body='$new_body' where announ_id='$addid'");
        echo "<script>alert('公告修改成功！');location.href='announce.php'</script>";
        exit;
    }
}


if ($action=="del"){
    $addid = $_POST["addid"];
    $db->query("DELETE FROM et_announ WHERE announ_id='$addid'");
    @unlink(ET_ROOT."/include/cache/userannounce.cache.php");
    echo "<script>alert('公告删除成功！');location.href='announce.php'</script>";
    exit;
}

$sql = "select announ_id,announ_body from et_announ order by announ_id desc";
$query = $db->query($sql);
while ($data= $db->fetch_array($query)) {
    $id=$data['announ_id'];
    $body=$data['announ_body'];
    $anounc[] = array("id"=>$id,"body"=>$body);
}

include($template->getfile('announce.htm'));
?>