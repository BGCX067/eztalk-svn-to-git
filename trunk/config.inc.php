<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

// 数据库配置
$server      =  "localhost"; 	            // 数据库服务器，一般为“localhost”
$db_username =  "root"; 	                // MySQL 用户名
$db_password =  ""; 		                // MySQL 用户密码
$db_name     =  ""; 	                    // 数据库名称
$pconnect    =  0;				            // 数据库持久连接 0=关闭, 1=打开
$webaddr     =  "http://localhost/et";      // 安装地址,末尾不加“/”

// 页面设置
$index_num=20;       //主页每次显示的条数
$home_num=20;        //我的博客每次显示的条数
$message_num=20;     //我的留言每次显示的条数
$fri_num=20;         //好友动态每次显示的条数
$favp_num=20;        //我的收藏每次显示的条数


//=====================以下信息不要修改=============================================

$cookiedomain = ''; 			// cookie 作用域
$cookiepath = '/';			// cookie 作用路径

$lastlogin=600;		//最后登录时间差
$version= "V4.0 Beta2";   //程序版本

//=====================机器人暂不支持，请不要修改以下信息============================
$openserver=0;
$serverip="127.0.0.1";
$serverport="8888";
$msnrobot="";
$qqrobot="";
$gtalkrobot="";
?>