<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

function UploadImage($upname,$smallmark=1,$dstsw,$dstsh=0,$path_dim,$path_xim,$newname,$smallname=0) {
    global $webaddr,$_FILES,$refer;
    $phpv=str_replace('.', '', PHP_VERSION);
    $filename=$upname;
    $max_file_size = 2147483648;        //�ϴ��ļ���С����, ��λBYTE 2m
    $path_im = $path_dim;               //���ɴ�ͼ�����ļ���·��
    $path_sim = $path_xim;              //����ͼ�����ļ���·��
    $simclearly=75;
    $simclearlypng =$phpv>=512?7:75;        //����ͼ������0-100������Խ��Խ�������ļ��ߴ�Խ��
    $smallmark = $smallmark;            //�Ƿ���������ͼ(1Ϊ������,����Ϊ��);
    $dst_sw =$dstsw;                   //��������ͼ��ȣ��߶��Ҳ��õȱ������ţ�����ֻҪ�ȽϿ�ȾͿ�����
    $uptypes=array(
        'image/jpg',
        'image/jpeg',
        'image/png',
        'image/pjpeg',
        'image/gif',
        'image/bmp',
        'image/x-png'
    );

    if (!is_uploaded_file($_FILES[$filename][tmp_name])) {
        if ($refer) {
            header("location:$refer&tip=1");
            exit;
        } else {
            echo "<div class='showmag'><p>�ϴ�ʧ�ܣ��ļ���������ļ������ڣ�</p><p><a href='index.php'>������ҳ</a></p></div>";
            wapfooter();
            exit;
        }
    }
    $file = $_FILES[$filename];

    if($max_file_size < $file["size"]) { //����ļ���С
        if ($refer) {
            header("location:$refer&tip=27");
            exit;
        } else {
            echo "<div class='showmag'><p>�ϴ�ͼƬ�����������2M֮�ڣ�</p><p><a href='index.php'>������ҳ</a></p></div>";
            wapfooter();
            exit;
        }
    }
    if(!in_array($file["type"],$uptypes)) { //����ļ�����
        if ($refer) {
            header("location:$refer&tip=2");
            exit;
        } else {
            echo "<div class='showmag'><p>�ϴ�ʧ�ܣ�ͼƬ��ʽ����ȷ��</p><p><a href='index.php'>������ҳ</a></p></div>";
            wapfooter();
            exit;
        }
    }
    if(!file_exists($path_im)) {    //����ϴ�Ŀ¼�Ƿ���ڣ������ڴ���
        mkdir($path_im);
    }

    $filename = $file["tmp_name"];
    $im_size = getimagesize($filename);

    $src_w = $im_size[0];
    $src_h = $im_size[1];
    $src_type = $im_size[2];

    $pinfo = pathinfo($file["name"]);
    $filetype = $pinfo['extension'];
    $all_path = $path_im.$newname.".".$filetype;   //·��+�ļ���,Ŀǰ���ϴ�ʱ������
    if (file_exists($all_path)) {
        @unlink($all_path);
    }
    if(!move_uploaded_file ($filename,$all_path)) {
        if ($refer) {
            header("location:$refer&tip=1");
            exit;
        } else {
            echo "<div class='showmag'><p>�ϴ�ʧ�ܣ������ļ������ڣ�</p><p><a href='index.php'>������ҳ</a></p></div>";
            wapfooter();
            exit;
        }
    }
    $pinfo = pathinfo($all_path);
    $fname = $pinfo[basename];

    switch($src_type)//�ж�ԴͼƬ�ļ�����
    {
         case 1://gif
         $src_im = imagecreatefromgif($all_path);//��ԴͼƬ�ļ�ȡ��ͼ��
         break;
         case 2://jpg
         $src_im = imagecreatefromjpeg($all_path);
         break;
         case 3://png
         $src_im = imagecreatefrompng($all_path);
         break;
         //case 6:
         //$src_im=imagecreatefromwbmp($all_path);
         //break;
         default:
         die("��֧�ֵ��ļ�����");
         exit;
    }

   if($smallmark == 1) {
       if(!file_exists($path_sim)) {   //�������ͼĿ¼�Ƿ���ڣ������ڴ���
           mkdir($path_sim);
       }
       if ($smallname) $newname=$smallname;
       $sall_path = $path_sim.$newname.".".$filetype;
       if (file_exists($sall_path)) {
           @unlink($sall_path);
       }
       if($src_w <= $dst_sw) { // ԭͼ�ߴ� <= ����ͼ�ߴ�
           if ($dstsh==0)  {
                $dst_sim = imagecreatetruecolor($src_w,$src_h); //�½�����ͼ���λͼ
                $sx=$sy=0;
           } else {
                $dst_sim = imagecreatetruecolor($dstsw,$dstsh); //�½�����ͼ���λͼ
                $sx=($dstsw-$src_w)/2;
                $sy=($dstsh-$src_h)/2;
           }
           $img = imagecreatefrompng("images/phbg.png");
           imagecopymerge($dst_sim,$img,0,0,0,0,$dstsw,$dstsh,100); //ԭͼͼ��д���½����λͼ��
           imagecopymerge($dst_sim,$src_im,$sx,$sy,0,0,$src_w,$src_h,100); //ԭͼͼ��д���½����λͼ��
       }

       if($src_w > $dst_sw) { // ԭͼ�ߴ� > ����ͼ�ߴ�
           $dst_sh = $dst_sw/$src_w*$src_h;
           if ($dst_sh<$dstsh) {
               $dst_sh=$dstsh;
               $dst_sw=$dst_sh/$src_h*$src_w;
           }
           if ($dstsh==0) {
                $dst_sim = imagecreatetruecolor($dst_sw,$dst_sh); //�½�����ͼ���λͼ���ȱ�����Сԭͼ�ߴ磩
           } else {
                $dst_sim = imagecreatetruecolor($dstsw,$dstsh); //�½�����ͼ���λͼ���ȱ�����Сԭͼ�ߴ磩
           }
           imagecopyresampled($dst_sim,$src_im,0,0,0,0,$dst_sw,$dst_sh,$src_w,$src_h); //ԭͼͼ��д���½����λͼ��
       }

       switch($src_type) {
            case 1:imagegif($dst_sim,$sall_path,$simclearly);//����gif�ļ���ͼƬ������0-100
            break;
            case 2:imagejpeg($dst_sim,$sall_path,$simclearly);//����jpg�ļ���ͼƬ������0-100
            break;
            case 3:imagepng($dst_sim,$sall_path,$simclearlypng);//����png�ļ���ͼƬ������0-100
            break;
            //case 6:
            //imagewbmp($dst_sim,$sall_path);
            break;
       }
       //�ͷŻ���
       imagedestroy($dst_sim);
}
    imagedestroy($src_im);
    return $newname.".".$filetype;
}
?>