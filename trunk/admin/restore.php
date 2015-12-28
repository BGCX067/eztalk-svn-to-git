<?php
define('is_admin_path', 'yes');
include('../common.inc.php');
include("../include/backup.func.php");

if ($admin_login !="yes" || $my[isadmin]!=1)
    header("Location: admin_login.php");

session_start();

$handle=opendir('./backup');
while ($file = readdir($handle)) {
    if(eregi("^[0-9]{8,8}([0-9a-z_]+)(\.sql)$",$file))
        $t1="<option value='$file'>$file</option>";
    $t=$t.$t1;
}
closedir($handle);

if ($action=="restore") {
    if($_POST['restorefrom']=="server"){
        if(!$_POST['serverfile']) {
            $msgs[]="��ѡ��ӷ������ļ��ָ����ݣ���û��ָ�������ļ�";
            $show=show_msg($msgs);
            include($template->getfile('restore.htm'));
            pageend();
        }
        if(!eregi("_v[0-9]+",$_POST['serverfile'])){
            $filename="./backup/".$_POST['serverfile'];
            if(import($filename))
                $msgs[]="�����ļ�".$_POST['serverfile']."�ɹ��������ݿ�";
            else
                $msgs[]="�����ļ�".$_POST['serverfile']."����ʧ��";
            $show=show_msg($msgs);
            include($template->getfile('restore.htm'));
            pageend();
        }else{
            $filename="./backup/".$_POST['serverfile'];
            if(import($filename))
                $msgs[]="�����ļ�".$_POST['serverfile']."�ɹ��������ݿ�";
            else {
                $msgs[]="�����ļ�".$_POST['serverfile']."����ʧ��";
                $show=show_msg($msgs);
                include($template->getfile('restore.htm'));
                pageend();
            }
            $voltmp=explode("_v",$_POST['serverfile']);
            $volname=$voltmp[0];
            $volnum=explode(".sq",$voltmp[1]);
            $volnum=intval($volnum[0])+1;
            $tmpfile=$volname."_v".$volnum.".sql";
            if(file_exists("./backup/".$tmpfile)){
                $msgs[]="������3���Ӻ��Զ���ʼ����˷־��ݵ���һ���ݣ��ļ�".$tmpfile."�������ֶ���ֹ��������У��������ݿ�ṹ����";
                $_SESSION['data_file']=$tmpfile;
                $show=show_msg($msgs);
                sleep(3);
                echo "<script language='javascript'>";
                echo "location='restore.php';";
                echo "</script>";
            }else{
                $msgs[]="�˷־���ȫ������ɹ�";
                $show=show_msg($msgs);
            }
        }
    }


    if($_POST['restorefrom']=="localpc"){
        switch ($_FILES['myfile']['error']){
            case 1:
            case 2:
            $msgs[]="���ϴ����ļ����ڷ������޶�ֵ���ϴ�δ�ɹ�";
            break;
            case 3:
            $msgs[]="δ�ܴӱ��������ϴ������ļ�";
            break;
            case 4:
            $msgs[]="�ӱ����ϴ������ļ�ʧ��";
            break;
            case 0:
            break;
        }
        if($msgs){
            $show=show_msg($msgs);
            include($template->getfile('restore.htm'));
            pageend();
        }
        $fname=date("Ymd",time())."_".sjs(5).".sql";
        if (is_uploaded_file($_FILES['myfile']['tmp_name'])) {
            copy($_FILES['myfile']['tmp_name'], "./backup/".$fname);
        }
        if (file_exists("./backup/".$fname)){
            $msgs[]="���ر����ļ��ϴ��ɹ�";
            if(import("./backup/".$fname)) {
                $msgs[]="���ر����ļ��ɹ��������ݿ�";
                unlink("./backup/".$fname);
            }else {
                $msgs[]="���ر����ļ��������ݿ�ʧ��";
            }
        } else {
            $msgs[]="�ӱ����ϴ������ļ�ʧ��";
        }
        $show=show_msg($msgs);
    }

    if(!$_POST['act'] && $_SESSION['data_file']) {
        $filename="./backup/".$_SESSION['data_file'];
        if(import($filename))
            $msgs[]="�����ļ�".$_SESSION['data_file']."�ɹ��������ݿ�";
        else {
            $msgs[]="�����ļ�".$_SESSION['data_file']."����ʧ��";
            $show=show_msg($msgs);
            include($template->getfile('restore.htm'));
            pageend();
        }
        $voltmp=explode("_v",$_SESSION['data_file']);
        $volname=$voltmp[0];
        $volnum=explode(".sq",$voltmp[1]);
        $volnum=intval($volnum[0])+1;
        $tmpfile=$volname."_v".$volnum.".sql";
        if(file_exists("./backup/".$tmpfile)){
            $msgs[]="������3���Ӻ��Զ���ʼ����˷־��ݵ���һ���ݣ��ļ�".$tmpfile."�������ֶ���ֹ��������У��������ݿ�ṹ����";
            $_SESSION['data_file']=$tmpfile;
            $show=show_msg($msgs);
            sleep(3);
            echo "<script language='javascript'>";
            echo "location='restore.php';";
            echo "</script>";
        }else{
            $msgs[]="�˷־���ȫ������ɹ�";
            unset($_SESSION['data_file']);
            $show=show_msg($msgs);
        }
    }
} else {
    $msgs[]="�������ڻָ��������ݵ�ͬʱ����ȫ������ԭ������";
    $msgs[]="���ݻָ�ֻ�ָܻ��ɱ�ϵͳ�����������ļ����������������ʽ�޷�ʶ��";
    $msgs[]="�ӱ��ػָ������������2m";
    $msgs[]="�����ʹ���˷־��ݣ�ֻ���ֹ������ļ���1�����������ļ�����ϵͳ����";
    $show=show_msg($msgs);
}

include($template->getfile('restore.htm'));
?>