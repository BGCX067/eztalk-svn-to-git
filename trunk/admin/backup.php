<?php
define('is_admin_path', 'yes');
include('../common.inc.php');
include("../include/backup.func.php");

if ($admin_login !="yes" || $my[isadmin]!=1)
    header("Location: admin_login.php");

$db->query("show table status from $db_name");
while($db->nextrecord()){
    $s1="<option value='".$db->f('Name')."'>".$db->f('Name')."</option>";
    $s=$s1.$s;
}

if ($action=="back") {
    $bftype=$_POST['bfzl'];
    if($bftype=="quanbubiao"){
        if(!$_POST['fenjuan']){ //���Ƿ־�
            if(!$tables=$db->query("show table status from $db_name")) {
                $msgs="�����ݿ�ṹ����";
                echo "<script>alert('$msgs');location.href='backup.php'</script>";
                exit;
            }
            $sql="";
            while($db->nextrecord($tables)) {
                $table=$db->f("Name");
                $sql.=make_header($table);
                $db->query("select * from $table");
                $num_fields=$db->nf();
                while($db->nextrecord()) {
                    $sql.=make_record($table,$num_fields);
                }
            }
            $filename=date("Ymd",time())."_all.sql";
            if($_POST['weizhi']=="localpc") down_file($sql,$filename);
            elseif($_POST['weizhi']=="server"){
                if(write_file($sql,$filename)) $msgs="ȫ�����ݱ����ݱ������,���ɱ����ļ�./backup/$filename";
                else $msgs="����ȫ�����ݱ�ʧ��";
                echo "<script>alert('$msgs');location.href='backup.php'</script>";
                exit;
            }
        }else{  //�־���
            if(!$_POST['filesize']){
                $msgs="����д�����ļ��־��С��";
                echo "<script>alert('$msgs');location.href='backup.php'</script>";
                exit;
            }
            if(!$tables=$db->query("show table status from $db_name")){
                $msgs="�����ݿ�ṹ����";
                echo "<script>alert('$msgs');location.href='backup.php'</script>";
                exit;
            }
            $sql="";
            $p=1;
            $filename=date("Ymd",time())."_all";
            while($db->nextrecord($tables)){
                $table=$db->f("Name");
                $sql.=make_header($table);
                $db->query("select * from $table");
                $num_fields=$db->nf();
                while($db->nextrecord()){
                    $sql.=make_record($table,$num_fields);
                    if(strlen($sql)>=$_POST['filesize']*1000){
                        $filename.=("_v".$p.".sql");
                        if(write_file($sql,$filename)) $msgs="ȫ�����ݱ�-��-".$p."-���ݱ������,���ɱ����ļ�./backup/$filename";
                        else $msgs="���ݱ�-".$_POST['tablename']."-ʧ��";
                        $p++;
                        $filename=date("Ymd",time())."_all";
                        $sql="";
                    }
                }
            }
            if($sql!=""){
                $filename.=("_v".$p.".sql");
                if(write_file($sql,$filename))$msgs="ȫ�����ݱ�-��-".$p."-���ݱ������,���ɱ����ļ�./backup/$filename";
            }
            echo "<script>alert('$msgs');location.href='backup.php'</script>";
            exit;
        }
    } elseif($bftype=="danbiao") {
        if(!$_POST['tablename']) {
            $msgs="��ѡ��Ҫ���ݵ����ݱ�";
            echo "<script>alert('$msgs');location.href='backup.php'</script>";
            exit;
        }
        if(!$_POST['fenjuan']){ //���־�
            $sql=make_header($_POST['tablename']);
            $db->query("select * from ".$_POST['tablename']);
            $num_fields=$db->nf();
            while($db->nextrecord()){
                $sql.=make_record($_POST['tablename'],$num_fields);
            }
            $filename=date("Ymd",time())."_".$_POST['tablename'].".sql";
            if($_POST['weizhi']=="localpc") down_file($sql,$filename);
            elseif($_POST['weizhi']=="server"){
                if(write_file($sql,$filename)) $msgs="��-".$_POST['tablename']."-���ݱ������,���ɱ����ļ�./backup/$filename";
                else $msgs="���ݱ�-".$_POST['tablename']."-ʧ��";
                echo "<script>alert('$msgs');location.href='backup.php'</script>";
                exit;
            }
        } else { //�־���
            if(!$_POST['filesize']){
                $msgs="����д�����ļ��־��С";
                echo "<script>alert('$msgs');location.href='backup.php'</script>";
                exit;
            }
            $sql=make_header($_POST['tablename']);
            $p=1;
            $filename=date("Ymd",time())."_".$_POST['tablename'];
            $db->query("select * from ".$_POST['tablename']);
            $num_fields=$db->nf();
            while ($db->nextrecord()){
                $sql.=make_record($_POST['tablename'],$num_fields);
                if(strlen($sql)>=$_POST['filesize']*1000){
                    $filename.=("_v".$p.".sql");
                    if(write_file($sql,$filename)) $msgs="��-".$_POST['tablename']."-��-".$p."-���ݱ������,���ɱ����ļ�./backup/$filename";
                    else $msgs="���ݱ�-".$_POST['tablename']."-ʧ��";
                    $p++;
                    $filename=date("Ymd",time())."_".$_POST['tablename'];
                    $sql="";
                }
           }
        }
        if($sql!="") {
            $filename.=("_v".$p.".sql");
            if(write_file($sql,$filename)) $msgs="��-".$_POST['tablename']."-��-".$p."-���ݱ������,���ɱ����ļ�./backup/$filename";
        }
        echo "<script>alert('$msgs');location.href='backup.php'</script>";
        exit;
    }
    if($_POST['weizhi']=="localpc" && $_POST['fenjuan']=='yes') {
        $msgs="ֻ��ѡ�񱸷ݵ�������������ʹ�÷־��ݹ���";
        echo "<script>alert('$msgs');location.href='backup.php'</script>";
        exit;
    }
    if($_POST['fenjuan']=="yes" && !$_POST['filesize']) {
        $msgs="��ѡ���˷־��ݹ��ܣ���δ��д�־��ļ���С";
        echo "<script>alert('$msgs');location.href='backup.php'</script>";
        exit;
    }
    if($_POST['weizhi']=="server" && !writeable("./backup")) {
        $msgs="�����ļ����Ŀ¼'./backup'����д�����޸�Ŀ¼����";
        echo "<script>alert('$msgs');location.href='backup.php'</script>";
        exit;
    }
}

include($template->getfile('backup.htm'));
?>
