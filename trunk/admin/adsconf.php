<?PHP
define('is_admin_path', 'yes');
include('../common.inc.php');

if ($admin_login !="yes" || $my[isadmin]!=1)
    header("Location: admin_login.php");

if ($action=="addads") {
    $addbody=daddslashes(trim($_POST['addbody']));
    $position=$_POST['position'];
    if ($addbody) {
        $db->query("INSERT INTO et_ads (position,adbody) VALUES ('$position','$addbody')");
        @unlink(ET_ROOT."/include/cache/ads.cache.php");

        echo "<script>alert('广告位更新成功！');location.href='adsconf.php'</script>";
        exit;
    } else {
        echo "<script>alert('您没有填写广告内容，返回重新填写！');location.href='adsconf.php'</script>";
        exit;
    }
}

if ($action=="edit") {
    $aid=$_POST['aid'];
    $adsbody=daddslashes(trim($_POST['editadsbody']));
    $position=$_POST['editposition'];
    if ($adsbody) {
        $db->query("UPDATE et_ads SET position='$position',adbody='$adsbody' where ad_id='$aid'");
        @unlink(ET_ROOT."/include/cache/ads.cache.php");
        echo "<script>alert('广告位更新成功！');location.href='adsconf.php'</script>";
        exit;
    } else {
        echo "<script>alert('您没有填写广告内容，返回重新填写！');location.href='adsconf.php'</script>";
        exit;
    }
}

if ($act=="del") {
    $adid=$_GET['adid'];

    $db->query("DELETE FROM et_ads where ad_id='$adid'");
    @unlink(ET_ROOT."/include/cache/ads.cache.php");
    echo "<script>alert('删除广告成功，点击返回！');location.href='adsconf.php'</script>";
    exit;
}

$sql = "select * from et_ads order by ad_id desc";
$query = $db->query($sql);
while ($data= $db->fetch_array($query)) {
    $ad_id=$data['ad_id'];
    $position=$data['position'];
    $adbody=$data['adbody'];

    $ads[] = array("ad_id"=>$ad_id,"position"=>$position,"adbody"=>$adbody);
}

include($template->getfile('adsconf.htm'));
?>