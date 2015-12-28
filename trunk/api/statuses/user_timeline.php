<?php
/*
显示用户的消息
路径： http://.../statuses/user_timeline.[json|xml|rss]
参数：
    * id (必选) - 用户 id，用户设隐私时需验证用户。
    示例： http://.../statuses/user_timeline.rss?id=1

    * count (可选) - 消息数，范围 1-20，默认为 20。
    示例： http://.../statuses/user_timeline.rss?id=1&count=10

    * page (可选) - 页码，从 1 开始
    示例： http://.../statuses/user_timeline.json?id=1&page=3

    * format (可选）－ 消息内容格式，当 format=html 时，返回消息的内容字段是进行@识别，网址识别等后台处理之后的html代码。
    * callback (可选) - JavaScript 函数名，使用 JSON 格式时可用，将 JSON 对象作为参数直接调用

    示例： http://.../statuses/user_timeline.json?id=1&callback=getStatuses
*/
$API=1;
include('../../common.inc.php');

$id=$_GET['id'];
$page=$_GET['page']?$_GET['page']:1;
$ext=$_GET['ext'];
$callback=$_GET['callback'];
$format=$_GET['format'];
$count=isset($_GET['count'])?$_GET['count']:20;
if ($count>20 || $count<1) $count=20;
$start=($page-1)*$count;

$sql = "select isclose,user_name from et_users where user_id='$id'";
$query = $db->query($sql);
$data = $db->fetch_array($query);
$isclose=$data['isclose'];
$username=$data['user_name'];
if (!$username) {
    echo "不存在该用户";
    exit;
} else {
    if ($isclose==1) {
        if ($PHP_AUTH_PW=="" || $PHP_AUTH_USER=="") {
            header("WWW-Authenticate: Basic realm=\"EasyTalk\"");
            header("HTTP/1.0 401 Unauthorized");
            echo "验证失败";
            exit;
        } else {
            $sql = "select user_id from et_users where mailadres='$PHP_AUTH_USER' && password='".md5(md5($PHP_AUTH_PW))."'";
            $query = $db->query($sql);
            $data = $db->fetch_array($query);
            if ($data) {
                $isfriend=isfriend($id,$data[user_id]);
                if ($isfriend[allfri]!=1 && $id!=$data[user_id]) {
                    echo "您和此用户不是好友关系，不能查看";
                    exit;
                }
            } else {
                header("WWW-Authenticate: Basic realm=\"EasyTalk\"");
                header("HTTP/1.0 401 Unauthorized");
                echo "验证失败";
                exit;
            }
        }
    }
}

//xml start
if ($ext=="xml") {
    header("Content-type: application/xml");
    echo "<?xml version=\"1.0\" encoding=\"gbk\"?>\n<statuses>\n";

    $sql ="SELECT s.content_id,s.content_body,s.posttime,s.type,s.status_id,s.status_uid,u.user_id,u.user_name,u.home_city,u.live_city,u.user_info,u.user_head,u.isclose,t.topic_id,t.topic_body,t.open FROM et_users AS u,et_content AS s left join et_topic AS t on s.topicid=t.topic_id && t.open=1 where s.user_id=u.user_id && s.user_id='$id' order by s.content_id desc limit $start,$count";
    $query = $db->query($sql);
    while($data = $db->fetch_array($query)){
        $xml_sid=$data['content_id'];
        $xml_uid=$data['user_id'];
        $xml_uname=$data['user_name'];
        $xml_homecity=($data['home_city']=="选择省份 选择城市" || $data['home_city']=="0")?"":$data['home_city'];
        $xml_livecity=($data['live_city']=="选择省份 选择城市" || $data['live_city']=="0")?"":$data['live_city'];
        $xml_uinfo=$data['user_info'];
        $xml_uhead=$data['user_head']?"$webaddr/attachments/head/".$data['user_head']:"$webaddr/images/noavatar.jpg";
        $xml_topicbody=$data['topic_body']?"<a href='$webaddr/op/topic/$data[topic_id]'>[".$data['topic_body']."]</a> ":"";
        $xml_cbody=$xml_topicbody.$data['content_body'];
        $xml_cbody=$format=="html"?htmlspecialchars($xml_cbody):clean_html($xml_cbody);
        $xml_cbody=apiurlreplace($xml_cbody);
        $xml_statusid=$data['status_id']?$data['status_id']:"";
        $xml_statusuid=$data['status_uid']?$data['status_uid']:"";
        $xml_stime=$data['posttime'];
        $xml_stime=gmdate('D M d H:i:s',$xml_stime+8*3600). " +0000 ".gmdate('Y',$xml_stime+8*3600);
        $xml_type=$data['type'];
        $xml_isclose=$data['isclose']==1?"true":"false";

        echo "<status>\n".
             "<created_at>$xml_stime</created_at>\n".
             "<id>$xml_sid</id>\n".
             "<text><![CDATA[$xml_cbody]]></text>\n".
             "<source><![CDATA[$xml_type]]></source>\n".
             "<reply_to_status_id>$xml_statusid</reply_to_status_id>\n".
             "<reply_to_user_id>$xml_statusuid</reply_to_user_id>\n".
             "<user>\n".
             "	<id>$xml_uid</id>\n".
             "	<name><![CDATA[$xml_uname]]></name>\n".
             "	<homelocation>$xml_homecity</homelocation>\n".
             "	<livelocation>$xml_livecity</livelocation>\n".
             "	<url>$webaddr/home/u.$xml_uid</url>\n".
             "	<description><![CDATA[$xml_uinfo]]></description>\n".
             "	<profile_image_url>$xml_uhead</profile_image_url>\n".
             "	<protected>$xml_isclose</protected>\n".
             "</user>\n".
             "</status>\n";
    }
    echo "</statuses>";
}
//xml end

//rss start
if ($ext=="rss") {
    header("Content-type: application/xml");
    echo "<?xml version=\"1.0\" encoding=\"gbk\"?>\n".
         "  <rss version=\"2.0\">\n".
         "  <channel>\n".
         "  <title>EasyTalk</title>\n".
         "  <link>$webaddr/home</link>\n".
         "  <description>EasyTalk - 看看 ".$username." 在做什么…</description>\n".
         "  <language>zh-cn</language>\n";
    $sql ="SELECT s.content_id,s.content_body,s.posttime,u.user_id,u.user_name,t.topic_id,t.topic_body,t.open FROM et_users AS u,et_content AS s left join et_topic AS t on s.topicid=t.topic_id && t.open=1 where s.user_id=u.user_id && s.user_id='$id' order by s.content_id desc limit $start,$count";
    $query = $db->query($sql);
    while($data = $db->fetch_array($query)){
        $rss_sid=$data['content_id'];
        $rss_uid=$data['user_id'];
        $rss_uname=$data['user_name'];
        $rss_topicbody=$data['topic_body']?"<a href='$webaddr/op/topic/$data[topic_id]'>[".$data['topic_body']."]</a> ":"";
        $rss_cbody=$rss_topicbody.$data['content_body'];
        $rss_cbody=$format=="html"?htmlspecialchars($rss_cbody):clean_html($rss_cbody);
        $rss_cbody=apiurlreplace($rss_cbody);
        $rss_stime=gmdate('Y-m-d H:i:s',$data['posttime']+8*3600);

        echo "    <item>\n".
             "     <title>$rss_uname: $rss_cbody</title>\n".
             "     <description><![CDATA[$rss_uname: $rss_cbody]]></description>\n".
             "     <pubDate>$rss_stime</pubDate>\n".
             "     <link>$webaddr/op/view/$rss_sid</link>\n".
             "   </item>\n";
         }
        echo "  </channel>\n".
             "  </rss>";
}
//rss end

//json start
if ($ext=="json") {
    header("Content-type: application/json");
    if ($callback) echo "$callback.callBack([";
    else echo "[";

    $sql ="SELECT s.content_id,s.content_body,s.posttime,s.type,s.status_id,s.status_uid,u.user_id,u.user_name,u.home_city,u.live_city,u.user_info,u.user_head,u.isclose,t.topic_id,t.topic_body,t.open FROM et_users AS u,et_content AS s left join et_topic AS t on s.topicid=t.topic_id && t.open=1 where s.user_id=u.user_id order && s.user_id='$id' by s.content_id desc limit $start,$count";
    $query = $db->query($sql);
    while($data = $db->fetch_array($query)){
        $json_sid=$data['content_id'];
        $json_uid=$data['user_id'];
        $json_uname=$data['user_name'];
        $json_homecity=($data['home_city']=="选择省份 选择城市" || $data['home_city']=="0")?"":$data['home_city'];
        $json_livecity=($data['live_city']=="选择省份 选择城市" || $data['live_city']=="0")?"":$data['live_city'];
        $json_uinfo=$data['user_info'];
        $json_uhead=$data['user_head']?"$webaddr/attachments/head/".$data['user_head']:"$webaddr/images/noavatar.jpg";
        $json_topicbody=$data['topic_body']?"<a href='$webaddr/op/topic/$data[topic_id]'>[".$data['topic_body']."]</a> ":"";
        $json_cbody=$json_topicbody.$data['content_body'];
        $json_cbody=$format=="html"?htmlspecialchars($json_cbody):clean_html($json_cbody);
        $json_cbody=apiurlreplace($json_cbody);
        $json_statusid=$data['status_id']?$data['status_id']:"";
        $json_statusuid=$data['status_uid']?$data['status_uid']:"";
        $json_stime=$data['posttime'];
        $json_stime=gmdate('Y-m-d H:m:s',$json_stime+8*3600);
        $json_isclose=$data['isclose']==1?"true":"false";

        $tem="{\"created_at\":\"$json_stime\",\"id\":\"$json_sid\",\"text\":\"$json_cbody\",\"reply_to_status_id\"=\"$json_statusid\",\"reply_to_user_id\"=\"$json_statusuid\",\"user\":{\"id\":\"$json_uid\",\"name\":\"$json_uname\",\"homelocation\":\"$json_homecity\",\"livelocation\":\"$json_livecity\",\"description\":\"$json_uinfo\",\"profile_image_url\":\"$json_uhead\",\"protected\":\"$json_isclose\",\"url\":\"$webaddr/home/u.$json_uid\"}},";
        $tem = str_replace(array("\r", "\n"), array("", "<br />"), $tem);
        $json_meg=$json_meg.$tem;
	}
	$json_meg=substr("$json_meg", 0, -1);
	echo $json_meg;
	if ($callback) echo "])";
	else echo "]";
}
//json end
?>