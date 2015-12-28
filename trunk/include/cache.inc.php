<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

//30分钟更新一次缓存
if (time()-@filemtime(ET_ROOT."/include/cache/userannounce.cache.php")>1800) {
    @unlink(ET_ROOT."/include/cache/userannounce.cache.php");
}

@include(ET_ROOT."/include/cache/setting.cache.php");
@include(ET_ROOT."/include/cache/pluginlist.cache.php");
@include(ET_ROOT."/include/cache/userannounce.cache.php");
@include(ET_ROOT."/include/cache/template.cache.php");
@include(ET_ROOT."/include/cache/topic.cache.php");
@include(ET_ROOT."/include/cache/ads.cache.php");

//设置 缓存
if (!file_exists(ET_ROOT."/include/cache/setting.cache.php")) {
	$query = "select * from et_settings";
	$result = $db->query($query);
	$data = $db->fetch_array($result);
	$webn1=$data['web_name'];
	$webn2=$data['web_name2'];
	$webm=$data['web_miibeian'];
	$seokey=$data['seokey'];
	$description=$data['description'];
	$templateid=$data['templateid'];
    $replace=$data['replace_word'];
    $mail_server=$data['mail_server'];
    $mail_port=$data['mail_port'];
    $mail_name=$data['mail_name'];
    $mail_user=$data['mail_user'];
    $mail_pass=$data['mail_pass'];

    $query2 = "select temp_dir from et_templates where temp_id='$templateid'";
	$result2 = $db->query($query2);
	$data2 = $db->fetch_array($result2);
	$temp_dir=$data2['temp_dir'];

	$fp=fopen(ET_ROOT."/include/cache/setting.cache.php","a");
	fputs($fp,"<?php\n");
    fputs($fp,"if(!defined('IN_ET')) {\n");
    fputs($fp,"   exit('Access Denied');\n");
    fputs($fp,"}\n\n");
    fputs($fp,"//设置信息缓存\n");
	fputs($fp,"\$webn1='$webn1';\n");
	fputs($fp,"\$webn2='$webn2';\n");
	fputs($fp,"\$webm='$webm';\n");
	fputs($fp,"\$seokey='$seokey';\n");
	fputs($fp,"\$description='$description';\n");
	fputs($fp,"\$templateid='$templateid';\n");
    fputs($fp,"\$temp_dir='$temp_dir';\n");
    fputs($fp,"\$replace='$replace';\n");
    fputs($fp,"\$mail_server='$mail_server';\n");
    fputs($fp,"\$mail_port='$mail_port';\n");
    fputs($fp,"\$mail_name='$mail_name';\n");
    fputs($fp,"\$mail_user='$mail_user';\n");
    fputs($fp,"\$mail_pass='$mail_pass';\n");
	fputs($fp,"?>");
    fclose($fp);
    @include(ET_ROOT."/include/cache/setting.cache.php");
}

//插件缓存
if (!file_exists(ET_ROOT."/include/cache/pluginlist.cache.php")) {
    $query = "SELECT plugin_name,plugin_identifier,plugin_open FROM et_plugins order by list_id desc";
    $result = $db->query($query);
    while($data = $db->fetch_array($result)) {
        $plugin_name=$data['plugin_name'];
        $plugin_identifier=$data['plugin_identifier'];
        $plugin_open=$data['plugin_open'];

        if ($plugin_open==1) {
            $pluginlisttem="<li><a href=\"$webaddr/plugin?pin=$plugin_identifier\">$plugin_name</a></li>";
            $pluginlist=$pluginlist.$pluginlisttem;
        }
    }
        $fp=fopen(ET_ROOT."/include/cache/pluginlist.cache.php","a");
        fputs($fp,"<?php\n");
        fputs($fp,"if(!defined('IN_ET')) {\n");
        fputs($fp,"   exit('Access Denied');\n");
        fputs($fp,"}\n\n");
        fputs($fp,"//插件缓存\n");
        fputs($fp,"\$pluginlist='$pluginlist';\n");
        fputs($fp,"?>");
        fclose($fp);
        @include(ET_ROOT."/include/cache/pluginlist.cache.php");
}

//专题缓存
if (!file_exists(ET_ROOT."/include/cache/topic.cache.php")) {
    $query = "SELECT * FROM et_topic where open=1 order by topic_id desc";
    $result = $db->query($query);
    while($data = $db->fetch_array($result)) {
        $topic_id=$data['topic_id'];
        $topic_body=$data['topic_body'];

        $topiclisttem="<a href=\"$webaddr/op/topic/$topic_id\" style=\"text-decoration:none;\"><font color=\"red\">[$topic_body]</font></a>&nbsp;";
        $topiclist=$topiclist.$topiclisttem;
    }
        $fp=fopen(ET_ROOT."/include/cache/topic.cache.php","a");
        fputs($fp,"<?php\n");
        fputs($fp,"if(!defined('IN_ET')) {\n");
        fputs($fp,"   exit('Access Denied');\n");
        fputs($fp,"}\n\n");
        fputs($fp,"//专题缓存\n");
        fputs($fp,"\$topiclist='$topiclist';\n");
        fputs($fp,"?>");
        fclose($fp);
        @include(ET_ROOT."/include/cache/topic.cache.php");
}

//推荐，新用户，公告缓存
if (!file_exists(ET_ROOT."/include/cache/userannounce.cache.php")) {
    $query_tj = $db->query("SELECT user_id,user_name,user_head FROM et_users where msg_num>0 && userlock='0' && user_head!='0' && home_city!='' && live_city!='' && user_gender!='' && userlock!=1 order by msg_num desc limit 8");
    while($data = $db->fetch_array($query_tj)) {
        $uhead=$data[user_head]?"$webaddr/attachments/head/".$data[user_head]:"$webaddr/images/noavatar.jpg";
        $tm1='<li><a title="'.$data[user_name].'" href="'.$webaddr.'/home/u.'.$data[user_id].'"><img alt="'.$data[user_name].'" src="'.$uhead.'"/><span>'.$data[user_name].'</span></a></li>';
        $tm=$tm.$tm1;
    }
    $query_new = $db->query("SELECT user_id,user_name,user_head FROM et_users where user_head!='0' && home_city!='' && live_city!='' && user_gender!=''  && userlock!=1 order by user_id desc limit 8");
    while($data = $db->fetch_array($query_new)) {
        $uhead=$data[user_head]?"$webaddr/attachments/head/".$data[user_head]:"$webaddr/images/noavatar.jpg";
        $ttm1='<li><a title="'.$data[user_name].'" href="'.$webaddr.'/home/u.'.$data[user_id].'"><img alt="'.$data[user_name].'" src="'.$uhead.'"/><span>'.$data[user_name].'</span></a></li>';
        $ttm=$ttm.$ttm1;
    }
    $query = $db->query("SELECT announ_body FROM et_announ order by announ_id desc");
    while($data = $db->fetch_array($query)) {
        $announ_body=$data['announ_body'];
        $temp1="<li>$announ_body</li>";
        $temp=$temp.$temp1;
    }
    $fp=fopen(ET_ROOT."/include/cache/userannounce.cache.php","a");
    fputs($fp,"<?php\n");
    fputs($fp,"if(!defined('IN_ET')) {\n");
    fputs($fp,"   exit('Access Denied');\n");
    fputs($fp,"}\n\n");
    fputs($fp,"//推荐，新用户，公告缓存\n");
    fputs($fp,"\$tuijian='$tm';\n");
    fputs($fp,"\$newcome='$ttm';\n");
    fputs($fp,"\$announce='$temp';\n");
    fputs($fp,"?>");
    fclose($fp);
    @include(ET_ROOT."/include/cache/userannounce.cache.php");
}

//模板 缓存
if (!file_exists(ET_ROOT."/include/cache/template.cache.php")) {
	$sql2 = "select varname,varbody from et_templatevars where temp_id='$templateid'";
    $query2 = $db->query($sql2);
    while($data2= $db->fetch_array($query2)) {
        $varname=trim($data2['varname']);
        $varbody=trim($data2['varbody']);
        $t1=explode("#",$varbody);
        $t2=explode(" ",$t1[1]);
        $t3=explode(" ",$varbody);
        if ($t2[1]) {
            $varbody=$t3[0]." url(".$webaddr."/images/".$temp_dir."/".$t3[1].")";
        }
        if($varname=="var_buttoncolor") {
            $varbody="border-color:$t3[0] $t3[1] $t3[2] $t3[3]; background:$t3[4]; color:$t3[5];";
        }
        $t="$".$varname."='".$varbody."';\n";
        $var=$var.$t;
    }

	$fp=fopen(ET_ROOT."/include/cache/template.cache.php","a");
	fputs($fp,"<?php\n");
    fputs($fp,"if(!defined('IN_ET')) {\n");
    fputs($fp,"   exit('Access Denied');\n");
    fputs($fp,"}\n\n");
    fputs($fp,"//模板缓存 模板 $templateid \n");
	fputs($fp,$var."\n");
	fputs($fp,"?>");
    fclose($fp);
    @include(ET_ROOT."/include/cache/template.cache.php");
}


//广告 缓存
if (!file_exists(ET_ROOT."/include/cache/ads.cache.php")) {
    $fp=fopen(ET_ROOT."/include/cache/ads.cache.php","a");
	fputs($fp,"<?php\n");
    fputs($fp,"if(!defined('IN_ET')) {\n");
    fputs($fp,"   exit('Access Denied');\n");
    fputs($fp,"}\n\n");
    fputs($fp,"//广告缓存 \n");

    $i=$j=$k=$l=0;
	$sql2 = "select * from et_ads";
    $query2 = $db->query($sql2);
    while($data2= $db->fetch_array($query2)) {
        $ad_id=$data2['ad_id'];
        $position=$data2['position'];
        $adbody=daddslashes($data2['adbody']);

        if ($position==1) {
            $i++;
            fputs($fp,"\$p1[$i]='$adbody';\n");
        } else if($position==2) {
            $j++;
            fputs($fp,"\$p2[$j]='$adbody';\n");
        } else if($position==3) {
            $k++;
            fputs($fp,"\$p3[$k]='$adbody';\n");
        } else if($position==4) {
            $l++;
            fputs($fp,"\$p4[$l]='$adbody';\n");
        }
    }
	fputs($fp,"?>");
    fclose($fp);
    @include(ET_ROOT."/include/cache/ads.cache.php");
}
?>