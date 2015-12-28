<?PHP
define('is_admin_path', 'yes');
include('../common.inc.php');

if ($admin_login !="yes" || $my[isadmin]!=1)
    header("Location: admin_login.php");

$pluginid=$_GET['pluid']?$_GET['pluid']:$_POST['pluid'];

if($pluginid) {
    $sql = "select * from et_plugins where plugin_id='$pluginid'";
    $query = $db->query($sql);
    $data= $db->fetch_array($query);
    $list_id=$data['list_id'];
    $plugin_name=$data['plugin_name'];
    $plugin_identifier=$data['plugin_identifier'];
    $plugin_path=$data['plugin_path'];
    $plugin_open=$data['plugin_open'];
    $plugin_info=$data['plugin_info'];
}

if(!$action && !$act){
    $sql = "select * from et_plugins order by plugin_id";
    $query = $db->query($sql);
    while ($data= $db->fetch_array($query)) {
        $plugin_id=$data['plugin_id'];
        $list_id=$data['list_id'];
        $plugin_name=$data['plugin_name'];
        $plugin_identifier=$data['plugin_identifier'];
        $plugin_path=$data['plugin_path'];
        $plugin_open=$data['plugin_open']==1?"是":"否";
        $plugin_info=$data['plugin_info'];

        $plugins[] = array("plugin_id"=>$plugin_id,"list_id"=>$list_id,"plugin_name"=>$plugin_name,"plugin_identifier"=>$plugin_identifier,"plugin_path"=>$plugin_path,"plugin_open"=>$plugin_open,"plugin_info"=>$plugin_info);
    }
}

if ($action=="edit") {
    $pname=daddslashes($_POST['pname']);
    $plistid=daddslashes($_POST['plistid']);
    $pidentifier=daddslashes(strtolower($_POST['pidentifier']));
    $ppath=daddslashes($_POST['ppath']);
    $pinfo=daddslashes($_POST['pinfo']);

    if (!preg_match("/^[a-zA-Z_]*$/i", $pidentifier) || strlen($pidentifier)>50) {
        echo "<script>alert('标识符只能使用英文字符且小于50个字符！');location.href='pluginconf.php?act=edit&pluid=$pluginid'</script>";
        exit;
    }
    if ($pidentifier!=$plugin_identifier) {
        $sql = "select plugin_identifier from et_plugins where plugin_identifier='$pidentifier'";
        $query = $db->query($sql);
        $data= $db->fetch_array($query);
        if ($data) {
            echo "<script>alert('此标识符已经存在，请更改！');location.href='pluginconf.php?act=edit&pluid=$pluginid'</script>";
            exit;
        }
    }
    if ($pname && $pidentifier && $ppath && $pinfo)
        $db->query("UPDATE et_plugins SET list_id='$plistid',plugin_name='$pname',plugin_identifier='$pidentifier',plugin_path='$ppath',plugin_info='$pinfo' where plugin_id='$pluginid'");

    @unlink(ET_ROOT."/include/cache/pluginlist.cache.php");

    header("location: pluginconf.php?act=edit&pluid=$pluginid");
}

if ($action=="newplugin") {
    $pname=daddslashes($_POST['pname']);
    $pidentifier=daddslashes(strtolower($_POST['pidentifier']));
    $ppath=daddslashes($_POST['ppath']);
    $pinfo=daddslashes($_POST['pinfo']);

    if (!preg_match("/^[a-zA-Z_]*$/i", $pidentifier) || strlen($pidentifier)>50) {
        echo "<script>alert('标识符只能使用英文字符且小于50个字符！');location.href='pluginconf.php'</script>";
        exit;
    }
    if($pname && $pidentifier && $ppath && $pinfo) {
        $sql = "select plugin_identifier from et_plugins where plugin_identifier='$pidentifier'";
        $query = $db->query($sql);
        $data= $db->fetch_array($query);
        if ($data) {
            echo "<script>alert('此标识符已经存在，请更改！');location.href='pluginconf.php?act=edit&pluid=$pluginid'</script>";
            exit;
        }

        $db->query("INSERT INTO et_plugins (plugin_name,plugin_identifier,plugin_path,plugin_info) VALUES ('$pname','$pidentifier','$ppath','$pinfo')");
    }
    @unlink(ET_ROOT."/include/cache/pluginlist.cache.php");

    header("location: pluginconf.php");
}

if($act=="del" && $plugin_name) {
    if ($plugin_open==0) {
        $db->query("DELETE FROM et_plugins where plugin_id='$pluginid'");
        @unlink(ET_ROOT."/include/cache/pluginlist.cache.php");
        echo "<script>alert('删除插件成功，点击返回！');location.href='pluginconf.php'</script>";
        exit;
    } else {
        echo "<script>alert('此插件已经开启不能删除！');location.href='pluginconf.php'</script>";
        exit;
    }
}

if ($act=="pluopen" && $plugin_name) {
    $db->query("UPDATE et_plugins SET plugin_open='1' where plugin_id='$pluginid'");
    @unlink(ET_ROOT."/include/cache/pluginlist.cache.php");
    header("location: pluginconf.php");
}

if ($act=="pluclose" && $plugin_name) {
    $db->query("UPDATE et_plugins SET plugin_open='0' where plugin_id='$pluginid'");
    @unlink(ET_ROOT."/include/cache/pluginlist.cache.php");
    header("location: pluginconf.php");
}

include($template->getfile('pluginconf.htm'));
?>