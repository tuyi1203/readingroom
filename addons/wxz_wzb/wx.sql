-- phpMyAdmin SQL Dump
-- version 4.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2017-04-28 16:17:26
-- ÊúçÂä°Âô®ÁâàÊú¨Ôºö 5.1.73
-- PHP Version: 5.3.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `we7`
--

-- --------------------------------------------------------

--
-- Ë°®ÁöÑÁªìÊûÑ `ims_wxz_wzb_banner`
--

CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_banner` (
  `id` int(10) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `url` text,
  `isshow` tinyint(1) DEFAULT NULL,
  `uniacid` int(10) DEFAULT NULL,
  `dateline` int(10) DEFAULT NULL,
  `sort` int(10) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Ë°®ÁöÑÁªìÊûÑ `ims_wxz_wzb_category`
--

CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_category` (
  `id` int(10) unsigned NOT NULL,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ÊâÄÂ±ûÂ∏êÂè∑',
  `title` varchar(50) NOT NULL COMMENT 'ÂàÜÁ±ªÂêçÁß∞',
  `isshow` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'ÊòØÂê¶ÊòæÁ§∫',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ÊéíÂ∫è',
  `dateline` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Ë°®ÁöÑÁªìÊûÑ `ims_wxz_wzb_comment`
--

CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_comment` (
  `id` int(10) NOT NULL,
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
  `dsamount` int(10) DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=784 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Ë°®ÁöÑÁªìÊûÑ `ims_wxz_wzb_ds`
--

CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_ds` (
  `id` int(10) unsigned NOT NULL,
  `uniacid` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `fee` varchar(20) NOT NULL DEFAULT '',
  `status` varchar(255) NOT NULL DEFAULT '0',
  `dateline` int(10) DEFAULT NULL,
  `rid` int(10) DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=213182 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Ë°®ÁöÑÁªìÊûÑ `ims_wxz_wzb_ds_setting`
--

CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_ds_setting` (
  `id` int(10) NOT NULL,
  `rid` int(10) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `settings` text,
  `dateline` int(10) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `isshow` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Ë°®ÁöÑÁªìÊûÑ `ims_wxz_wzb_help`
--

CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_help` (
  `id` int(10) NOT NULL,
  `share_uid` int(10) DEFAULT NULL,
  `help_uid` int(10) DEFAULT NULL,
  `dateline` int(10) DEFAULT NULL,
  `uniacid` int(10) DEFAULT NULL,
  `rid` int(10) DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Ë°®ÁöÑÁªìÊûÑ `ims_wxz_wzb_list`
--

CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_list` (
  `id` int(10) NOT NULL,
  `uniacid` int(10) unsigned NOT NULL,
  `title` varchar(200) NOT NULL,
  `list_share_img` varchar(255) NOT NULL,
  `list_share_title` varchar(255) NOT NULL,
  `list_share_desc` varchar(255) NOT NULL,
  `dateline` int(10) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Ë°®ÁöÑÁªìÊûÑ `ims_wxz_wzb_live_menu`
--

CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_live_menu` (
  `id` int(11) NOT NULL,
  `rid` int(10) DEFAULT '0',
  `sort` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `isshow` tinyint(1) NOT NULL,
  `type` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `settings` text NOT NULL,
  `dateline` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Ë°®ÁöÑÁªìÊûÑ `ims_wxz_wzb_live_pic`
--

CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_live_pic` (
  `id` int(10) NOT NULL,
  `uniacid` int(10) DEFAULT NULL,
  `content` text,
  `publisher` varchar(255) DEFAULT NULL,
  `dateline` int(10) DEFAULT NULL,
  `images` varchar(255) DEFAULT NULL,
  `pic` text,
  `rid` int(10) DEFAULT '0',
  `title` varchar(255) DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Ë°®ÁöÑÁªìÊûÑ `ims_wxz_wzb_live_red_packet`
--

CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_live_red_packet` (
  `id` int(11) unsigned NOT NULL,
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
  `withdraw_min` int(10) DEFAULT '100'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Ë°®ÁöÑÁªìÊûÑ `ims_wxz_wzb_live_setting`
--

CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_live_setting` (
  `id` int(10) NOT NULL,
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
  `istruenum` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Ë°®ÁöÑÁªìÊûÑ `ims_wxz_wzb_live_video_type`
--

CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_live_video_type` (
  `id` int(10) NOT NULL,
  `type` int(10) DEFAULT NULL,
  `settings` text,
  `dateline` int(10) DEFAULT NULL,
  `rid` int(10) DEFAULT NULL,
  `uniacid` int(10) DEFAULT '0',
  `player_weight` int(10) DEFAULT '1280',
  `player_height` int(10) DEFAULT '720'
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Ë°®ÁöÑÁªìÊûÑ `ims_wxz_wzb_packet_log`
--

CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_packet_log` (
  `id` int(10) unsigned NOT NULL,
  `uniacid` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `fee` varchar(20) NOT NULL DEFAULT '',
  `status` varchar(255) NOT NULL,
  `dateline` int(10) DEFAULT NULL,
  `type` tinyint(1) DEFAULT NULL,
  `rid` int(10) DEFAULT '0',
  `fromid` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Ë°®ÁöÑÁªìÊûÑ `ims_wxz_wzb_paylog`
--

CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_paylog` (
  `id` int(10) NOT NULL,
  `uniacid` int(10) unsigned NOT NULL,
  `rid` int(10) DEFAULT NULL,
  `uid` int(10) DEFAULT NULL,
  `lid` int(10) DEFAULT NULL,
  `amount` int(10) DEFAULT '0',
  `status` tinyint(1) DEFAULT '0',
  `type` tinyint(1) DEFAULT '0' COMMENT '1 ÷±≤•º‰∏∂∑— 2 ÷±≤•º‰¥Ú…Õ',
  `dateline` int(10) DEFAULT NULL,
  `transid` varchar(255) DEFAULT NULL,
  `intotime` int(10) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=868 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Ë°®ÁöÑÁªìÊûÑ `ims_wxz_wzb_red_packet`
--

CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_red_packet` (
  `id` int(11) unsigned NOT NULL,
  `uniacid` int(11) unsigned NOT NULL DEFAULT '0',
  `mchid` varchar(100) NOT NULL DEFAULT '' COMMENT '…Ãªß∫≈',
  `password` varchar(2550) NOT NULL DEFAULT '' COMMENT '…Ãªß√‹¬Î',
  `appid` varchar(100) NOT NULL DEFAULT '' COMMENT '∑˛ŒÒ∫≈ID',
  `secret` varchar(255) NOT NULL DEFAULT '' COMMENT '∑˛ŒÒ∫≈secret',
  `ip` varchar(100) NOT NULL DEFAULT '' COMMENT '∑˛ŒÒ∆˜IP',
  `sname` varchar(100) NOT NULL DEFAULT '' COMMENT 'π´÷⁄∫≈√˚≥∆',
  `wishing` varchar(100) NOT NULL DEFAULT '' COMMENT '◊£∏£”Ô',
  `actname` varchar(100) NOT NULL DEFAULT '' COMMENT '∫Ï∞¸ªÓ∂Ø√˚≥∆',
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
  `withdraw_min` int(10) DEFAULT '100'
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Ë°®ÁöÑÁªìÊûÑ `ims_wxz_wzb_roll_adv`
--

CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_roll_adv` (
  `id` int(10) NOT NULL,
  `uniacid` int(10) DEFAULT NULL,
  `rid` int(10) DEFAULT NULL,
  `content` text,
  `type` tinyint(1) DEFAULT NULL,
  `dateline` int(10) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
<div class="notice"><img src="themes/dot.gif" title="" alt="" class="icon ic_s_notice" /> Ëá™Âä®Âú®Êü•ËØ¢ÁªìÂ∞æÈó≠Âêà‰∫ÜÂºïÂè∑ÔºÅ</div>
-- --------------------------------------------------------

--
-- Ë°®ÁöÑÁªìÊûÑ `ims_wxz_wzb_setting`
--

CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_setting` (
  `id` int(10) unsigned NOT NULL,
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
  `getip_addr` text NOT NULL COMMENT 'œﬁ÷∆µÿ«¯ip',
  `yc_url` varchar(255) DEFAULT NULL,
  `yc` tinyint(1) DEFAULT '0',
  `no_avatar` varchar(255) DEFAULT NULL,
  `gz_must` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Ë°®ÁöÑÁªìÊûÑ `ims_wxz_wzb_share`
--

CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_share` (
  `id` int(10) NOT NULL,
  `share_uid` int(10) DEFAULT NULL,
  `help_uid` int(10) DEFAULT NULL,
  `dateline` int(10) DEFAULT NULL,
  `amount` int(10) DEFAULT NULL,
  `uniacid` int(10) DEFAULT NULL,
  `rid` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Ë°®ÁöÑÁªìÊûÑ `ims_wxz_wzb_spread_adv`
--

CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_spread_adv` (
  `id` int(10) NOT NULL,
  `uniacid` int(10) DEFAULT NULL,
  `rid` int(10) DEFAULT NULL,
  `images` varchar(255) DEFAULT NULL,
  `count_time` int(10) DEFAULT NULL,
  `type` tinyint(1) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `dateline` int(10) DEFAULT NULL,
  `bgcolor` varchar(255) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `timecolor` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Ë°®ÁöÑÁªìÊûÑ `ims_wxz_wzb_tx`
--

CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_tx` (
  `id` int(10) unsigned NOT NULL,
  `uniacid` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `fee` varchar(20) NOT NULL DEFAULT '',
  `dateline` int(10) DEFAULT NULL,
  `type` tinyint(1) DEFAULT NULL,
  `rid` int(10) DEFAULT '0',
  `fromid` int(10) DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=213077 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Ë°®ÁöÑÁªìÊûÑ `ims_wxz_wzb_user`
--

CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_user` (
  `id` int(10) NOT NULL,
  `uniacid` int(10) DEFAULT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `headimgurl` varchar(255) DEFAULT NULL,
  `province` varchar(255) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `sex` varchar(255) DEFAULT NULL,
  `dateline` int(10) DEFAULT NULL,
  `sub_openid` varchar(255) DEFAULT NULL COMMENT '∂©‘ƒ∫≈openid',
  `openid` varchar(255) DEFAULT NULL COMMENT '∑˛ŒÒ∫≈openid',
  `rid` int(10) DEFAULT '0',
  `password` text
) ENGINE=InnoDB AUTO_INCREMENT=158 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Ë°®ÁöÑÁªìÊûÑ `ims_wxz_wzb_viewer`
--

CREATE TABLE IF NOT EXISTS `ims_wxz_wzb_viewer` (
  `id` int(10) NOT NULL,
  `uid` int(10) DEFAULT NULL,
  `rid` int(10) DEFAULT NULL,
  `dateline` int(10) DEFAULT NULL,
  `share` tinyint(1) DEFAULT '0',
  `amount` int(10) DEFAULT '0',
  `ispay` tinyint(1) DEFAULT '0',
  `rlog` varchar(255) DEFAULT NULL,
  `deposit` int(10) DEFAULT '0',
  `password` text
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ims_wxz_wzb_banner`
--
ALTER TABLE `ims_wxz_wzb_banner`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ims_wxz_wzb_category`
--
ALTER TABLE `ims_wxz_wzb_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ims_wxz_wzb_comment`
--
ALTER TABLE `ims_wxz_wzb_comment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ims_wxz_wzb_ds`
--
ALTER TABLE `ims_wxz_wzb_ds`
  ADD PRIMARY KEY (`id`), ADD KEY `uid` (`uid`), ADD KEY `uniacid` (`uniacid`), ADD KEY `status` (`status`);

--
-- Indexes for table `ims_wxz_wzb_ds_setting`
--
ALTER TABLE `ims_wxz_wzb_ds_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ims_wxz_wzb_help`
--
ALTER TABLE `ims_wxz_wzb_help`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ims_wxz_wzb_list`
--
ALTER TABLE `ims_wxz_wzb_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ims_wxz_wzb_live_menu`
--
ALTER TABLE `ims_wxz_wzb_live_menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ims_wxz_wzb_live_pic`
--
ALTER TABLE `ims_wxz_wzb_live_pic`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ims_wxz_wzb_live_red_packet`
--
ALTER TABLE `ims_wxz_wzb_live_red_packet`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ims_wxz_wzb_live_setting`
--
ALTER TABLE `ims_wxz_wzb_live_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ims_wxz_wzb_live_video_type`
--
ALTER TABLE `ims_wxz_wzb_live_video_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ims_wxz_wzb_packet_log`
--
ALTER TABLE `ims_wxz_wzb_packet_log`
  ADD PRIMARY KEY (`id`), ADD KEY `uid` (`uid`), ADD KEY `uniacid` (`uniacid`), ADD KEY `status` (`status`);

--
-- Indexes for table `ims_wxz_wzb_paylog`
--
ALTER TABLE `ims_wxz_wzb_paylog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ims_wxz_wzb_red_packet`
--
ALTER TABLE `ims_wxz_wzb_red_packet`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ims_wxz_wzb_roll_adv`
--
ALTER TABLE `ims_wxz_wzb_roll_adv`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ims_wxz_wzb_setting`
--
ALTER TABLE `ims_wxz_wzb_setting`
  ADD PRIMARY KEY (`id`), ADD KEY `uniacid` (`uniacid`);

--
-- Indexes for table `ims_wxz_wzb_share`
--
ALTER TABLE `ims_wxz_wzb_share`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ims_wxz_wzb_spread_adv`
--
ALTER TABLE `ims_wxz_wzb_spread_adv`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ims_wxz_wzb_tx`
--
ALTER TABLE `ims_wxz_wzb_tx`
  ADD PRIMARY KEY (`id`), ADD KEY `uid` (`uid`), ADD KEY `uniacid` (`uniacid`);

--
-- Indexes for table `ims_wxz_wzb_user`
--
ALTER TABLE `ims_wxz_wzb_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ims_wxz_wzb_viewer`
--
ALTER TABLE `ims_wxz_wzb_viewer`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ims_wxz_wzb_banner`
--
ALTER TABLE `ims_wxz_wzb_banner`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `ims_wxz_wzb_category`
--
ALTER TABLE `ims_wxz_wzb_category`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `ims_wxz_wzb_comment`
--
ALTER TABLE `ims_wxz_wzb_comment`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=784;
--
-- AUTO_INCREMENT for table `ims_wxz_wzb_ds`
--
ALTER TABLE `ims_wxz_wzb_ds`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=213182;
--
-- AUTO_INCREMENT for table `ims_wxz_wzb_ds_setting`
--
ALTER TABLE `ims_wxz_wzb_ds_setting`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `ims_wxz_wzb_help`
--
ALTER TABLE `ims_wxz_wzb_help`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=70;
--
-- AUTO_INCREMENT for table `ims_wxz_wzb_list`
--
ALTER TABLE `ims_wxz_wzb_list`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `ims_wxz_wzb_live_menu`
--
ALTER TABLE `ims_wxz_wzb_live_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=26;
--
-- AUTO_INCREMENT for table `ims_wxz_wzb_live_pic`
--
ALTER TABLE `ims_wxz_wzb_live_pic`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=73;
--
-- AUTO_INCREMENT for table `ims_wxz_wzb_live_red_packet`
--
ALTER TABLE `ims_wxz_wzb_live_red_packet`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_wxz_wzb_live_setting`
--
ALTER TABLE `ims_wxz_wzb_live_setting`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=31;
--
-- AUTO_INCREMENT for table `ims_wxz_wzb_live_video_type`
--
ALTER TABLE `ims_wxz_wzb_live_video_type`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `ims_wxz_wzb_packet_log`
--
ALTER TABLE `ims_wxz_wzb_packet_log`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_wxz_wzb_paylog`
--
ALTER TABLE `ims_wxz_wzb_paylog`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=868;
--
-- AUTO_INCREMENT for table `ims_wxz_wzb_red_packet`
--
ALTER TABLE `ims_wxz_wzb_red_packet`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `ims_wxz_wzb_roll_adv`
--
ALTER TABLE `ims_wxz_wzb_roll_adv`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `ims_wxz_wzb_setting`
--
ALTER TABLE `ims_wxz_wzb_setting`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=37;
--
-- AUTO_INCREMENT for table `ims_wxz_wzb_share`
--
ALTER TABLE `ims_wxz_wzb_share`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_wxz_wzb_spread_adv`
--
ALTER TABLE `ims_wxz_wzb_spread_adv`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_wxz_wzb_tx`
--
ALTER TABLE `ims_wxz_wzb_tx`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=213077;
--
-- AUTO_INCREMENT for table `ims_wxz_wzb_user`
--
ALTER TABLE `ims_wxz_wzb_user`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=158;
--
-- AUTO_INCREMENT for table `ims_wxz_wzb_viewer`
--
ALTER TABLE `ims_wxz_wzb_viewer`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=108;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
