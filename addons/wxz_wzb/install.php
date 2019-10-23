<?php
pdo_query("

CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_banner` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `img` varchar(255) DEFAULT NULL,
  `url` text,
  `isshow` tinyint(1) DEFAULT NULL,
  `uniacid` int(10) DEFAULT NULL,
  `dateline` int(10) DEFAULT NULL,
  `sort` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(50) NOT NULL,
  `isshow` tinyint(1) NOT NULL DEFAULT '0',
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  `dateline` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_comment` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `uniacid` int(10) DEFAULT NULL,
  `content` text,
  `dateline` int(10) DEFAULT NULL,
  `is_auth` tinyint(1) DEFAULT '0',
  `nickname` varchar(255) DEFAULT NULL,
  `headimgurl` varchar(255) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `rid` int(10) DEFAULT '0',
  `lid` int(10) DEFAULT '0',
  `touid` int(10) DEFAULT '0',
  `tonickname` varchar(255) DEFAULT NULL,
  `toheadimgurl` varchar(255) DEFAULT NULL,
  `toid` int(10) DEFAULT '0',
  `isadmin` tinyint(1) DEFAULT '0',
  `ispacket` tinyint(1) DEFAULT '0',
  `amount` int(10) DEFAULT '0',
  `num` int(10) DEFAULT '0',
  `type` tinyint(1) DEFAULT '0',
  `send_num` int(10) DEFAULT '0',
  `yifa_amount` int(10) DEFAULT '0',
  `samount` text,
  `syifa` text,
  `dsid` int(10) DEFAULT '0',
  `dsstatus` int(1) DEFAULT '0',
  `dsamount` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_ds` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `fee` varchar(20) NOT NULL DEFAULT '',
  `status` varchar(255) NOT NULL DEFAULT '0',
  `dateline` int(10) DEFAULT NULL,
  `rid` int(10) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE,
  KEY `uniacid` (`uniacid`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_ds_setting` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `rid` int(10) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `settings` text,
  `dateline` int(10) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `isshow` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_help` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `share_uid` int(10) DEFAULT NULL,
  `help_uid` int(10) DEFAULT NULL,
  `dateline` int(10) DEFAULT NULL,
  `uniacid` int(10) DEFAULT NULL,
  `rid` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=70 DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_list` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL,
  `title` varchar(200) NOT NULL,
  `list_share_img` varchar(255) NOT NULL,
  `list_share_title` varchar(255) NOT NULL,
  `list_share_desc` varchar(255) NOT NULL,
  `dateline` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_live_menu` (

  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rid` int(10) DEFAULT '0',
  `sort` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `isshow` tinyint(1) NOT NULL,
  `type` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `settings` text NOT NULL,
  `dateline` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_live_pic` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) DEFAULT NULL,
  `content` text,
  `publisher` varchar(255) DEFAULT NULL,
  `dateline` int(10) DEFAULT NULL,
  `images` varchar(255) DEFAULT NULL,
  `pic` text,
  `rid` int(10) DEFAULT '0',
  `title` varchar(255) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_live_red_packet` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) unsigned NOT NULL DEFAULT '0',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0',
  `min` int(10) DEFAULT '0',
  `max` int(10) DEFAULT '0',
  `type` tinyint(1) DEFAULT '0',
  `reward_amount_min` int(10) DEFAULT '0',
  `reward_amount_max` int(10) DEFAULT '0',
  `pool_amount` int(10) DEFAULT '0',
  `send_amount` int(10) DEFAULT '0',
  `packet_rule` text,
  `rid` int(10) DEFAULT '0',
  `withdraw_min` int(10) DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_live_setting` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `sort` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `is_auth` tinyint(1) DEFAULT NULL,
  `dateline` int(10) DEFAULT NULL,
  `start_at` int(10) DEFAULT NULL,
  `end_at` int(10) DEFAULT NULL,
  `uniacid` int(10) DEFAULT NULL,
  `rule` text,
  `images` varchar(255) DEFAULT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `reward` tinyint(1) DEFAULT '0',
  `base_num` int(10) DEFAULT '0',
  `num_float` int(10) DEFAULT '0',
  `total_num` int(10) DEFAULT '0',
  `float_type` tinyint(1) DEFAULT '0',
  `real_num` int(10) DEFAULT '0',
  `theme` varchar(255) DEFAULT NULL,
  `rid` int(10) DEFAULT '0',
  `limit` tinyint(10) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `delayed` int(10) DEFAULT NULL,
  `amount` int(10) DEFAULT NULL,
  `cid` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `img` varchar(300) NOT NULL,
  `isshow` int(11) NOT NULL,
  `player_height` int(11) NOT NULL DEFAULT '180',
  `recommend` tinyint(1) DEFAULT '0',
  `button_show` tinyint(1) DEFAULT '0',
  `bgcolor` varchar(255) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `timecolor` varchar(255) DEFAULT NULL,
  `istruenum` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_live_video_type` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` int(10) DEFAULT NULL,
  `settings` text,
  `dateline` int(10) DEFAULT NULL,
  `rid` int(10) DEFAULT NULL,
  `uniacid` int(10) DEFAULT '0',
  `player_weight` int(10) DEFAULT '1280',
  `player_height` int(10) DEFAULT '720',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_packet_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `fee` varchar(20) NOT NULL DEFAULT '',
  `status` varchar(255) NOT NULL,
  `dateline` int(10) DEFAULT NULL,
  `type` tinyint(1) DEFAULT NULL,
  `rid` int(10) DEFAULT '0',
  `fromid` int(10) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE,
  KEY `uniacid` (`uniacid`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_paylog` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL,
  `rid` int(10) DEFAULT NULL,
  `uid` int(10) DEFAULT NULL,
  `lid` int(10) DEFAULT NULL,
  `amount` int(10) DEFAULT '0',
  `status` tinyint(1) DEFAULT '0',
  `type` tinyint(1) DEFAULT '0' COMMENT '1 直播间付费 2 直播间打赏',
  `dateline` int(10) DEFAULT NULL,
  `transid` varchar(255) DEFAULT NULL,
  `intotime` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_red_packet` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) unsigned NOT NULL DEFAULT '0',
  `mchid` varchar(100) NOT NULL DEFAULT '' COMMENT '商户号',
  `password` varchar(2550) NOT NULL DEFAULT '' COMMENT '商户密码',
  `appid` varchar(100) NOT NULL DEFAULT '' COMMENT '服务号ID',
  `secret` varchar(255) NOT NULL DEFAULT '' COMMENT '服务号secret',
  `ip` varchar(100) NOT NULL DEFAULT '' COMMENT '服务器IP',
  `sname` varchar(100) NOT NULL DEFAULT '' COMMENT '公众号名称',
  `wishing` varchar(100) NOT NULL DEFAULT '' COMMENT '祝福语',
  `actname` varchar(100) NOT NULL DEFAULT '' COMMENT '红包活动名称',
  `logo` varchar(255) NOT NULL DEFAULT '' COMMENT 'logo',
  `apiclient_cert` text COMMENT 'apiclient_cert',
  `apiclient_key` text COMMENT 'apiclient_key',
  `rootca` text COMMENT 'rootca',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0',
  `min` int(10) DEFAULT '0',
  `max` int(10) DEFAULT '0',
  `type` tinyint(1) DEFAULT '0',
  `reward_amount_min` int(10) DEFAULT '0',
  `reward_amount_max` int(10) DEFAULT '0',
  `pool_amount` int(10) DEFAULT '0',
  `send_amount` int(10) DEFAULT '0',
  `packet_rule` text,
  `rid` int(10) DEFAULT '0',
  `withdraw_min` int(10) DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_roll_adv` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) DEFAULT NULL,
  `rid` int(10) DEFAULT NULL,
  `content` text,
  `type` tinyint(1) DEFAULT NULL,
  `dateline` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_setting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL,
  `share_img` varchar(255) NOT NULL,
  `share_title` varchar(255) NOT NULL,
  `share_desc` varchar(255) NOT NULL,
  `title` varchar(200) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `sub_title` varchar(200) NOT NULL,
  `attention_url` varchar(200) NOT NULL,
  `loan_secret` varchar(255) DEFAULT NULL,
  `loan_appid` varchar(255) DEFAULT NULL,
  `attention_code` varchar(255) DEFAULT NULL,
  `rid` int(10) DEFAULT '0',
  `getip` tinyint(1) DEFAULT '0',
  `getip_addr` text NOT NULL COMMENT '限制地区ip',
  `yc_url` varchar(255) DEFAULT NULL,
  `yc` tinyint(1) DEFAULT '0',
  `no_avatar` varchar(255) DEFAULT NULL,
  `gz_must` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_share` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `share_uid` int(10) DEFAULT NULL,
  `help_uid` int(10) DEFAULT NULL,
  `dateline` int(10) DEFAULT NULL,
  `amount` int(10) DEFAULT NULL,
  `uniacid` int(10) DEFAULT NULL,
  `rid` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_spread_adv` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) DEFAULT NULL,
  `rid` int(10) DEFAULT NULL,
  `images` varchar(255) DEFAULT NULL,
  `count_time` int(10) DEFAULT NULL,
  `type` tinyint(1) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `dateline` int(10) DEFAULT NULL,
  `bgcolor` varchar(255) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `timecolor` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_tx` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `fee` varchar(20) NOT NULL DEFAULT '',
  `dateline` int(10) DEFAULT NULL,
  `type` tinyint(1) DEFAULT NULL,
  `rid` int(10) DEFAULT '0',
  `fromid` int(10) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE,
  KEY `uniacid` (`uniacid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) DEFAULT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `headimgurl` varchar(255) DEFAULT NULL,
  `province` varchar(255) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `sex` varchar(255) DEFAULT NULL,
  `dateline` int(10) DEFAULT NULL,
  `sub_openid` varchar(255) DEFAULT NULL COMMENT '订阅号openid',
  `openid` varchar(255) DEFAULT NULL COMMENT '服务号openid',
  `rid` int(10) DEFAULT '0',
  `password` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_viewer` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) DEFAULT NULL,
  `rid` int(10) DEFAULT NULL,
  `dateline` int(10) DEFAULT NULL,
  `share` tinyint(1) DEFAULT '0',
  `amount` int(10) DEFAULT '0',
  `ispay` tinyint(1) DEFAULT '0',
  `rlog` varchar(255) DEFAULT NULL,
  `deposit` int(10) DEFAULT '0',
  `password` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");