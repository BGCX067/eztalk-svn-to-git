<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

// ���ݿ�����
$server      =  "localhost"; 	            // ���ݿ��������һ��Ϊ��localhost��
$db_username =  "root"; 	                // MySQL �û���
$db_password =  ""; 		                // MySQL �û�����
$db_name     =  ""; 	                    // ���ݿ�����
$pconnect    =  0;				            // ���ݿ�־����� 0=�ر�, 1=��
$webaddr     =  "http://localhost/et";      // ��װ��ַ,ĩβ���ӡ�/��

// ҳ������
$index_num=20;       //��ҳÿ����ʾ������
$home_num=20;        //�ҵĲ���ÿ����ʾ������
$message_num=20;     //�ҵ�����ÿ����ʾ������
$fri_num=20;         //���Ѷ�̬ÿ����ʾ������
$favp_num=20;        //�ҵ��ղ�ÿ����ʾ������


//=====================������Ϣ��Ҫ�޸�=============================================

$cookiedomain = ''; 			// cookie ������
$cookiepath = '/';			// cookie ����·��

$lastlogin=600;		//����¼ʱ���
$version= "V4.0 Beta2";   //����汾

//=====================�������ݲ�֧�֣��벻Ҫ�޸�������Ϣ============================
$openserver=0;
$serverip="127.0.0.1";
$serverport="8888";
$msnrobot="";
$qqrobot="";
$gtalkrobot="";
?>