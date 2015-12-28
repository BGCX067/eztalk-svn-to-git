<?php
$API=1;
include('../common.inc.php');

$id=$_GET['id']; //用户ID
$count=isset($_GET['count'])?$_GET['count']:20; //范围 1-20，默认为 20
if ($count>20)
    $count=20;

if ($id) {
    header("Content-type: application/xml");
    $sql ="SELECT count(*) AS count FROM et_content AS s,et_users AS u where s.user_id=u.user_id && s.user_id='$id'";
    $query = $db->query($sql);
    $row = $db->fetch_array($query);
    $total=$row['count'];

    $sql ="SELECT user_name FROM et_users where user_id='$id'";
    $query = $db->query($sql);
    $row = $db->fetch_array($query);
    $uname=$row['user_name'];
    $uname=iconv("GB2312","UTF-8",$uname);

    if (!$uname) $err="1022";
    else $err="1009";
    echo "<?xml version=\"1.0\" encoding=\"gbk\"?>\n".
         "<root>\n".
         "<err>$err</err>\n".
         "<info>\n".
         "<pg>\n".
         "<num>$count</num>\n".
         "<total>$total</total>\n".
         "</pg>\n".

         "<us>\n".
         "<me>$uname</me>\n".
         "<mesn>$id</mesn>\n".
         "</us>\n";

    $sql = "SELECT s.*,u.user_name,u.user_head,u.user_info FROM et_content AS s,et_users AS u where s.user_id=u.user_id && s.user_id='$id' order by posttime desc limit $count";
    $query = $db->query($sql);
    while($data = $db->fetch_array($query)){
        $xml_sid=$data['content_id'];
        $xml_uid=$data['user_id'];
        $xml_uname=$data['user_name'];
        $xml_uinfo=$data['user_info'];
        $xml_uhead=$data['user_head']?"$webaddr/attachments/head/".$data['user_head']:"images/noavatar.jpg";
        $xml_cbody=trim(clean_html($data['content_body']))==""?"此条TALK暂时无法显示":trim(clean_html($data['content_body']));
        $xml_cbody=apiurlreplace($xml_cbody);
        $xml_cbody=iconv("GB2312","UTF-8",$xml_cbody);
        $xml_stime=$data['posttime'];
        $xml_stime=gmdate('y-m-d H:m:s',$xml_stime+8*3600);
        $xml_type=$data['type'];
        $xml_type=iconv("GB2312","UTF-8",$xml_type);
        $t= "<ml>\n".
             "<id>$xml_sid</id>\n".
             "<uid>$xml_uid</uid>\n".
             "<cn>$xml_cbody</cn>\n".
             "<t>$xml_stime</t>\n".
             "<sr>$xml_type</sr>\n".
             "</ml>\n";
        $res=$res.$t;
    }
    $res=$res?"<md>\n".$res."</md>\n":"";
    echo "$res</info>\n".
         "</root>\n";
}
?>