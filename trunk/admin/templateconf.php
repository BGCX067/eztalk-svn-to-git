<?php
define('is_admin_path', 'yes');
include('../common.inc.php');

if ($admin_login !="yes" || $my[isadmin]!=1)
    header("Location: admin_login.php");

$temid=$_GET['temid']?$_GET['temid']:$_POST['temid'];
if($temid) {
    //模板信息
    $sql = "select * from et_templates where temp_id='$temid'";
    $query = $db->query($sql);
    $data= $db->fetch_array($query);
    $temp_name=$data['temp_name'];
    $temp_dir=$data['temp_dir'];
    $temp_isused=$data['temp_isused'];
    //模板变量
    $sql2 = "select varid,varname,varbody from et_templatevars where temp_id='$temid'";
    $query2 = $db->query($sql2);
    while($data2= $db->fetch_array($query2)) {
        $varid=$data2['varid'];
        $varname=$data2['varname'];
        $varbody=$data2['varbody'];
        if (get_substr($varname,0,4)=="def_") {
            $defvar[] = array("varid"=>$varid,"varname"=>$varname,"varbody"=>$varbody);
        }
        $vars[$varname] = $varbody;
    }
}

if(!$action && !$act){
    $sql = "select * from et_templates order by temp_id";
    $query = $db->query($sql);
    while ($data= $db->fetch_array($query)) {
        $temp_id=$data['temp_id'];
        $temp_name=$data['temp_name'];
        $temp_dir=$data['temp_dir'];
        $temp_isused=$data['temp_isused']==1?"是":"否";

        $templ[] = array("temp_id"=>$temp_id,"temp_name"=>$temp_name,"temp_dir"=>$temp_dir,"temp_isused"=>$temp_isused);
    }
}

if($act=="del" && $temp_name) {
    if ($temp_isused==0) {
        $db->query("DELETE FROM et_templates where temp_id='$temid'");
        $db->query("DELETE FROM et_templatevars where temp_id='$temid'");

        echo "<script>alert('删除模板成功，点击返回！');location.href='templateconf.php'</script>";
        exit;
    } else if($temid==1 || $temp_isused==1){
        echo "<script>alert('此模板是默认使用的不能删除！');location.href='templateconf.php'</script>";
        exit;
    }
}

if($action=="import") {
    $import=preg_replace("/(#.*\s+)*/", '',$_POST['import']);
    $ignoreversion=$_POST['ignoreversion'];

    if($import) {
        $import=daddslashes(unserialize(base64_decode($import)));
        if(!is_array($import)) {
			echo "<script>alert('很抱歉，您导入的文件不被系统所识别或者文件已损坏！');location.href='templateconf.php'</script>";
            exit;
		} elseif(empty($ignoreversion) && strip_tags($import['version']) != strip_tags($version)) {
			echo "<script>alert('很抱歉，您导入的模板版本和目前EasyTalk的版本不一致，如果你确认此模板可用，请勾选“忽略版本限制”选项！');location.href='templateconf.php'</script>";
            exit;
		} else {
           $t=$db->query("INSERT INTO et_templates (temp_name,temp_dir) VALUES ('$import[temp_name]','$import[temp_dir]')");
           if ($t==1) {
               $nexttid=mysql_insert_id();

               foreach($import as $im_varname => $im_varbody) {
                   if ($im_varname!="temp_name" && $im_varname!="temp_dir" && $im_varname!="version") {
                        $im_varbody = @htmlspecialchars($im_varbody);
                        $db->query("INSERT INTO et_templatevars (temp_id,varname,varbody) VALUES ('$nexttid','$im_varname','$im_varbody')");
                   }
               }

               echo "<script>alert('恭喜您，模板文件导入成功了！');location.href='templateconf.php'</script>";
               exit;
           }
        }
    } else {
        echo "<script>alert('导入文件为空，请重新导入！');location.href='templateconf.php'</script>";
        exit;
    }
}

if($action=="edit" && $temp_name) {
    $var_tempname=daddslashes($_POST['tempname']);
    $var_tempdir=daddslashes($_POST['tempdir']);

    $editvar=daddslashes($_POST['editvar']);
    $deleteid=$_POST['delete'];

    $newvar=daddslashes($_POST['newvar']);
    $newvarbody=daddslashes($_POST['newvarbody']);

    if($ids = implodeids($deleteid)) {
        $db->query("DELETE FROM et_templatevars where varid in ($ids) && temp_id='$temid'");
    }

    if ($newvar && $newvarbody) {
        $newvar=strtolower("def_".$newvar);

        $querye = $db->query("select varname from et_templatevars where temp_id='$temid' && varname='$newvar'");
        $datae= $db->fetch_array($querye);
        if ($datae) {
            echo "<script>alert('很抱歉，您填写的新变量名已经存在，请重新填写！');location.href='templateconf.php?act=edit&temid=$temid'</script>";
            exit;
        }
        if (strlen($newvar)>24) {
            echo "<script>alert('很抱歉，您填写的新变量名过长，系统允许的长度为20，请重新填写！');location.href='templateconf.php?act=edit&temid=$temid'</script>";
            exit;
        }
        if (!preg_match("/^[a-zA-Z0-9_]*$/i", $newvar)) {
            echo "<script>alert('很抱歉，您填写的新变量名有误，请重新填写！');location.href='templateconf.php?act=edit&temid=$temid'</script>";
            exit;
        }
        $db->query("INSERT INTO et_templatevars (temp_id,varname,varbody) VALUES ('$temid','$newvar','$newvarbody')");
    }
    //模板信息
    $db->query("UPDATE et_templates SET temp_name='$var_tempname',temp_dir='$var_tempdir' where temp_id='$temid'");
    //变量
    foreach($editvar as $ed_varname => $ed_varbody) {
        $ed_varbody = @htmlspecialchars($ed_varbody);
        $db->query("UPDATE et_templatevars SET varbody='$ed_varbody' where varname='$ed_varname' && temp_id='$temid'");
    }

    @unlink(ET_ROOT."/include/cache/template.cache.php");
    @unlink(ET_ROOT.'/templates/'.$temp_dir.'/cache/style.css');
    @unlink(ET_ROOT.'/templates/'.$temp_dir.'/cache/style.css.cache.php');

    echo "<script>alert('模板更新成功，点击确定返回！');location.href='templateconf.php?act=edit&temid=$temid'</script>";
    exit;
}

if($act=="export" && $temp_name) {
    $sql2 = "select varname,varbody from et_templatevars where temp_id='$temid'";
    $query2 = $db->query($sql2);
    while($data2= $db->fetch_array($query2)) {
        $varname=$data2['varname'];
        $varbody=$data2['varbody'];
        $vars[$varname] = $varbody;
    }
    $vars[temp_name]=$temp_name;
    $vars[temp_dir]=$temp_dir;
    $vars[version]=$version;

    $style_export = "# EasyTalk Style Dump\n".
			"# Version: EasyTalk $version\n".
			"# Time: ".date("Y-m-d H:m:s")."\n".
			"# From: $webaddr\n".
			"#\n".
			"# This file was BASE64 encoded\n".
			"# --------------------------------------------------------\n\n\n".
			wordwrap(base64_encode(serialize($vars)), 50, "\n", 1);

	ob_end_clean();
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: no-cache');
	header('Content-Encoding: none');
	header('Content-Length: '.strlen($style_export));
	header('Content-Disposition: attachment; filename=EasyTalk_style_'.$temp_name.'.txt');
	header('Content-Type: '.(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') ? 'application/octetstream' : 'application/octet-stream'));

	echo $style_export;
	exit();
}

if($act=="setused" && $temp_name) {
    $db->query("UPDATE et_templates SET temp_isused='0' where temp_isused='1'");
    $db->query("UPDATE et_templates SET temp_isused='1' where temp_id='$temid'");
    $db->query("UPDATE et_settings SET templateid='$temid'");

    @unlink(ET_ROOT."/include/cache/template.cache.php");
    @unlink(ET_ROOT.'/templates/'.$temp_dir.'/cache/style.css');
    @unlink(ET_ROOT.'/templates/'.$temp_dir.'/cache/style.css.cache.php');
    @unlink(ET_ROOT."/include/cache/setting_cache.php");
    header("location:templateconf.php");
}

include($template->getfile('templateconf.htm'));
?>