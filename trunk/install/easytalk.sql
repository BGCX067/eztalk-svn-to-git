DROP TABLE IF EXISTS et_ads;
CREATE TABLE et_ads (
  ad_id smallint(6) NOT NULL auto_increment,
  position smallint(1) NOT NULL,
  adbody text character set gbk NOT NULL,
  PRIMARY KEY  (ad_id)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk;


DROP TABLE IF EXISTS et_album;
CREATE TABLE et_album (
  album_id smallint(6) NOT NULL auto_increment,
  user_id smallint(6) NOT NULL,
  album_name varchar(20) character set gbk NOT NULL,
  face_photo varchar(30) character set gbk default NULL,
  photo_num smallint(5) NOT NULL default '0',
  PRIMARY KEY  (album_id)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk ;


DROP TABLE IF EXISTS et_announ;
CREATE TABLE et_announ (
  announ_id smallint(6) NOT NULL auto_increment,
  announ_body varchar(200) NOT NULL,
  announ_time int(10) NOT NULL,
  PRIMARY KEY  (announ_id)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk ;


DROP TABLE IF EXISTS et_content;
CREATE TABLE et_content (
  content_id smallint(6) NOT NULL auto_increment,
  user_id smallint(6) NOT NULL,
  topicid smallint(6) NOT NULL default '0',
  content_body varchar(300) NOT NULL,
  posttime int(10) NOT NULL,
  type varchar(10) NOT NULL default '网页',
  status_id smallint(6) NOT NULL default '0',
  status_uid smallint(6) NOT NULL default '0',
  status_uname varchar(64) NOT NULL default '0',
  status_type varchar(10) NOT NULL default '0',
  PRIMARY KEY  (content_id),
  FULLTEXT KEY content_body (content_body)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk ;


DROP TABLE IF EXISTS et_favorite;
CREATE TABLE et_favorite (
  fav_id smallint(6) NOT NULL auto_increment,
  content_id smallint(6) NOT NULL,
  sc_uid smallint(6) NOT NULL,
  PRIMARY KEY  (fav_id)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk ;


DROP TABLE IF EXISTS et_friend;
CREATE TABLE et_friend (
  fri_id smallint(6) NOT NULL auto_increment,
  fid_jieshou smallint(6) NOT NULL,
  fid_fasong smallint(6) NOT NULL,
  PRIMARY KEY  (fri_id)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk ;


DROP TABLE IF EXISTS et_messages;
CREATE TABLE et_messages (
  message_id smallint(6) NOT NULL auto_increment,
  js_id smallint(6) NOT NULL,
  fs_id smallint(6) NOT NULL,
  message_body varchar(300) NOT NULL,
  m_time int(10) NOT NULL,
  isread smallint(1) NOT NULL default '0',
  PRIMARY KEY  (message_id)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk ;


DROP TABLE IF EXISTS et_photos;
CREATE TABLE et_photos (
  pt_id smallint(6) NOT NULL auto_increment,
  al_id smallint(6) NOT NULL,
  user_id smallint(6) NOT NULL,
  pt_name varchar(30) character set gbk NOT NULL,
  pt_title varchar(20) character set gbk default NULL,
  uploadtime int(10) NOT NULL,
  PRIMARY KEY  (pt_id)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk ;


DROP TABLE IF EXISTS et_plugins;
CREATE TABLE et_plugins (
  plugin_id smallint(6) NOT NULL auto_increment,
  list_id smallint(2) NOT NULL default '0',
  plugin_name varchar(50) NOT NULL,
  plugin_identifier varchar(50) NOT NULL,
  plugin_path varchar(100) NOT NULL,
  plugin_open smallint(1) NOT NULL default '0',
  plugin_info text,
  PRIMARY KEY  (plugin_id)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk;


DROP TABLE IF EXISTS et_settings;
CREATE TABLE et_settings (
  web_name varchar(64) NOT NULL,
  web_name2 varchar(64) NOT NULL,
  web_miibeian varchar(64) NOT NULL,
  seokey varchar(200) NOT NULL,
  description varchar(200) NOT NULL,
  replace_word varchar(500) NOT NULL default '0',
  templateid smallint(6) NOT NULL default '1',
  mail_server varchar(30) NOT NULL default 'smtp.163.com',
  mail_port smallint(5) NOT NULL default '25',
  mail_name varchar(30) NOT NULL default 'user@163.com',
  mail_user varchar(30) NOT NULL default 'user',
  mail_pass varchar(30) NOT NULL default 'pass',
  PRIMARY KEY  (web_name)
) ENGINE=MyISAM DEFAULT CHARSET=gbk;


INSERT INTO et_settings (web_name, web_name2, web_miibeian, seokey, description, replace_word, templateid, mail_server, mail_port, mail_name, mail_user, mail_pass) VALUES
('EasyTalk', '迷你博客', '正在备案……', 'EasyTalk', 'EasyTalk,blog', '', 1, 'smtp.163.com', 25, '', '', '');


DROP TABLE IF EXISTS et_share;
CREATE TABLE et_share (
  share_id smallint(6) NOT NULL auto_increment,
  user_id smallint(6) NOT NULL,
  link_data text character set gbk NOT NULL,
  content varchar(250) character set gbk NOT NULL,
  sharetime int(10) NOT NULL,
  type varchar(10) character set gbk NOT NULL,
  retimes smallint(6) NOT NULL default '0',
  PRIMARY KEY  (share_id)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk ;


DROP TABLE IF EXISTS et_sharereply;
CREATE TABLE et_sharereply (
  shre_id smallint(6) NOT NULL auto_increment,
  share_id smallint(6) NOT NULL,
  user_id smallint(6) NOT NULL,
  reply_body varchar(250) character set gbk NOT NULL,
  reply_time int(10) NOT NULL,
  PRIMARY KEY  (shre_id)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk ;


DROP TABLE IF EXISTS et_sign;
CREATE TABLE et_sign (
  user_id smallint(6) NOT NULL,
  sign1 text character set gbk,
  sign1time int(10) default NULL,
  UNIQUE KEY user_id (user_id)
) ENGINE=MyISAM DEFAULT CHARSET=gbk ;


DROP TABLE IF EXISTS et_templates;
CREATE TABLE et_templates (
  temp_id smallint(6) NOT NULL auto_increment,
  temp_name varchar(30) character set gbk NOT NULL,
  temp_dir varchar(100) character set gbk NOT NULL,
  temp_isused smallint(1) NOT NULL default '0',
  PRIMARY KEY  (temp_id)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk ;


INSERT INTO et_templates (temp_id, temp_name, temp_dir, temp_isused) VALUES
(1, '默认模板', 'default', 1);


DROP TABLE IF EXISTS et_templatevars;
CREATE TABLE et_templatevars (
  varid smallint(6) NOT NULL auto_increment,
  temp_id smallint(6) NOT NULL,
  varname varchar(50) character set gbk NOT NULL,
  varbody varchar(50) character set gbk NOT NULL,
  PRIMARY KEY  (varid)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk ;

INSERT INTO et_templatevars (varid, temp_id, varname, varbody) VALUES
(1, 1, 'var_logo', 'logo.png'),
(2, 1, 'var_bg', '#acdae5 bg.png'),
(3, 1, 'var_fontcolor', '#222'),
(4, 1, 'var_fontsize', '12px'),
(5, 1, 'var_lineheight', '150%'),
(6, 1, 'var_linkfontcolor', '#06c'),
(7, 1, 'var_font', 'Tahoma, Helvetica, sans-serif'),
(8, 1, 'var_lowcolor', '#999'),
(9, 1, 'var_buttoncolor', '#66acff #094fa1 #094fa1 #66acff #2680e9 #fff');


DROP TABLE IF EXISTS et_topic;
CREATE TABLE et_topic (
  topic_id smallint(6) NOT NULL auto_increment,
  topic_body varchar(20) NOT NULL,
  open smallint(1) NOT NULL default '0',
  PRIMARY KEY  (topic_id)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk ;


DROP TABLE IF EXISTS et_users;
CREATE TABLE et_users (
  user_id smallint(6) NOT NULL auto_increment,
  user_name varchar(64) NOT NULL,
  password varchar(64) NOT NULL,
  user_head varchar(200) NOT NULL default '0',
  mailadres varchar(100) NOT NULL,
  home_city varchar(16) default NULL,
  live_city varchar(16) default NULL,
  birthday varchar(10) default NULL,
  signupdate varchar(20) NOT NULL,
  user_gender varchar(8) default NULL,
  user_info varchar(255) NOT NULL default '这家伙很懒，什么也没写。',
  isadmin tinyint(1) NOT NULL default '0',
  isclose tinyint(1) NOT NULL default '0',
  issendmsg tinyint(1) NOT NULL default '0',
  last_login int(10) NOT NULL default '0',
  musicaddr varchar(200) default NULL,
  msg_num smallint(6) NOT NULL default '0',
  friend_num smallint(6) NOT NULL default '0',
  fav_num smallint(6) NOT NULL default '0',
  photo_num smallint(6) NOT NULL default '0',
  share_num smallint(6) NOT NULL default '0',
  qq int(15) default NULL,
  msn varchar(50) default NULL,
  gtalk varchar(50) default NULL,
  getmsgtype varchar(10) default NULL,
  theme_bgcolor varchar(7) default NULL,
  theme_pictype varchar(10) default NULL,
  theme_text varchar(7) default NULL,
  theme_link varchar(7) default NULL,
  theme_sidebar varchar(7) default NULL,
  theme_sidebox varchar(7) default NULL,
  theme_bgurl varchar(200) default NULL,
  auth_email varchar(50) NOT NULL default '0',
  userlock tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (user_id)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk ;


DROP TABLE IF EXISTS et_usertemplates;
CREATE TABLE et_usertemplates (
  ut_id smallint(6) NOT NULL auto_increment,
  theme_bgcolor varchar(7) character set gbk NOT NULL,
  theme_pictype varchar(10) character set gbk NOT NULL,
  theme_text varchar(7) character set gbk NOT NULL,
  theme_link varchar(7) character set gbk NOT NULL,
  theme_sidebar varchar(7) character set gbk NOT NULL,
  theme_sidebox varchar(7) character set gbk NOT NULL,
  theme_upload tinyint(1) NOT NULL,
  isopen tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (ut_id)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk ;

INSERT INTO et_usertemplates (ut_id, theme_bgcolor, theme_pictype, theme_text, theme_link, theme_sidebar, theme_sidebox, theme_upload, isopen) VALUES
(1, '#acdae5', 'center', '#000000', '#0066cc', '#e2f2da', '#b2d1a3', 1, 1),
(2, '#ccecfa', 'left', '#29453c', '#737311', '#faffe0', '#d5d9bf', 1, 1),
(3, '#3a9dcf', 'left', '#21282B', '#00ABFF', '#346C89', '#346C89', 1, 1),
(4, '#942970', 'repeat', '#660044', '#d957ad', '#ffbfea', '#d9a3c7', 1, 1),
(5, '#CCECFA', 'center', '#FFB300', '#00CDFF', '#303336', '#FF7700', 1, 1);