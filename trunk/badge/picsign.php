<?php
$API=1;
include('../common.inc.php');
$uid = $_GET['uid'];

function addtext($text1,$len,$textadd="") {
    $i=0;
    $text2="";

    if($len%2==1)
    $len=$len+1;
    $len1=StrLenW($text1);

    for($i=0;$i<$len1/$len;$i++){
        $text2.=get_substr($text1,$I,$len).$textadd;
        $text1=get_substr($text1,$len,$len1-$len);
    }
    return $text2;
}

$sql= "select * from et_sign where user_id='$uid'";
$query = $db->query($sql);
$data = $db->fetch_array($query);
$signdata=base64_decode($data["sign1"]);
$signtime=$data["sign1time"];
if(!$data) {
   $db->query("INSERT INTO et_sign  (user_id) VALUES ('$uid')");
   $sql= "select * from et_sign where user_id='$uid'";
   $query = $db->query($sql);
   $data = $db->fetch_array($query);
   $signdata=base64_decode($data["sign1"]);
   $signtime=$data["sign1time"];
}

if ($addtime-$signtime>3600) {
    if ($uid) {
        $sql= "select user_name,user_head from et_users where user_id='$uid'";
        $query = $db->query($sql);
        $data = $db->fetch_array($query);
        $name=$data['user_name'];
        $uheader=$data['user_head']?"../attachments/head/".$data['user_head']:"../images/noavatar.jpg";
        if (!file_exists($uheader)) $uheader="../images/noavatar.jpg";
        if ($name) {
            ob_start();
            $sql = "select content_body,posttime,type from et_content where user_id='$uid' order by content_id desc limit 1";
            $query = $db->query($sql);
            $data = $db->fetch_array($query);
            $content=$data['content_body'];
            $content=apiurlreplace($content);
            $content=clean_html($content);
            $type=$data['type'];
            $ctime=gmdate('Y-m-d H:i:s',$data['posttime']+8*3600);
            $img = imagecreatefrompng("../images/sign.png");
            $pic = imagecreatefromjpeg($uheader);
            imagecopy ($img, $pic, 10, 10, 0, 0, 96, 96);

            $textColor = imagecolorallocate($img, 0,0,255);
            $string = iconv("GB2312", "UTF-8", "$name");
            imagettftext($img,15,0,120,25,$textColor,"../include/simhei.ttf",$string);

            $content=get_substr($content,0,95);
            if ($content=="") {
                $content="尚未发表信息！ ".gmdate('Y-m-d H:i:s',time()+8*3600);
            } else {
                $content=$content." ".$ctime." 通过".$type;
            }
            $content=addtext($content,32,"\n");

            $textColor = imagecolorallocate($img, 130,130,130);
            $string = iconv("GB2312", "UTF-8", "$content");
            imagettftext($img,10,0,120,45,$textColor,"../include/simhei.ttf",$string);
            imagejpeg($img);
            $outmsg=base64_encode(ob_get_contents());
            ob_end_clean();
            $db->query("UPDATE et_sign SET sign1='$outmsg',sign1time='$addtime' where user_id='$uid'");
        }
    } else {
        echo "无效的用户ID!";
    }
}
header("Content-type: image/png");
echo $signdata;
exit;
?>
