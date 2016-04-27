-- phpMyAdmin SQL Dump
-- version 4.3.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2016-04-21 15:27:06
-- 服务器版本： 5.5.37-log
-- PHP Version: 5.4.27

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
-- 表的结构 `ims_sz_yi_adv`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_adv` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `advname` varchar(50) DEFAULT '',
  `link` varchar(255) DEFAULT '',
  `thumb` varchar(255) DEFAULT '',
  `displayorder` int(11) DEFAULT '0',
  `enabled` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_af_supplier`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_af_supplier` (
  `id` int(11) NOT NULL,
  `openid` varchar(255) CHARACTER SET utf8 NOT NULL,
  `uniacid` int(11) NOT NULL,
  `realname` varchar(55) CHARACTER SET utf8 NOT NULL,
  `mobile` varchar(255) CHARACTER SET utf8 NOT NULL,
  `weixin` varchar(255) CHARACTER SET utf8 NOT NULL,
  `productname` varchar(255) CHARACTER SET utf8 NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_article`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_article` (
  `id` int(11) NOT NULL,
  `article_title` varchar(255) NOT NULL DEFAULT '' COMMENT '文章标题',
  `resp_desc` text NOT NULL COMMENT '回复介绍',
  `resp_img` text NOT NULL COMMENT '回复图片',
  `article_content` longtext,
  `article_category` int(11) NOT NULL DEFAULT '0' COMMENT '文章分类',
  `article_date_v` varchar(20) NOT NULL DEFAULT '' COMMENT '虚拟发布时间',
  `article_date` varchar(20) NOT NULL DEFAULT '' COMMENT '文章发布时间',
  `article_mp` varchar(50) NOT NULL DEFAULT '' COMMENT '公众号',
  `article_author` varchar(20) NOT NULL DEFAULT '' COMMENT '发布作者',
  `article_readnum_v` int(11) NOT NULL DEFAULT '0' COMMENT '虚拟阅读量',
  `article_readnum` int(11) NOT NULL DEFAULT '0' COMMENT '真实阅读量',
  `article_likenum_v` int(11) NOT NULL DEFAULT '0' COMMENT '虚拟点赞数',
  `article_likenum` int(11) NOT NULL DEFAULT '0' COMMENT '真实点赞数',
  `article_linkurl` varchar(300) NOT NULL DEFAULT '' COMMENT '阅读原文链接',
  `article_rule_daynum` int(11) NOT NULL DEFAULT '0' COMMENT '每人每天参与次数',
  `article_rule_allnum` int(11) NOT NULL DEFAULT '0' COMMENT '所有参与次数',
  `article_rule_credit` int(11) NOT NULL DEFAULT '0' COMMENT '增加y积分',
  `article_rule_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '增加z余额',
  `article_rule_money_total` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '最高累计奖金',
  `article_rule_userd_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '截止目前累计奖励金额',
  `page_set_option_nocopy` int(1) NOT NULL DEFAULT '0' COMMENT '页面禁止复制url',
  `page_set_option_noshare_tl` int(1) NOT NULL DEFAULT '0' COMMENT '页面禁止分享至朋友圈',
  `page_set_option_noshare_msg` int(1) NOT NULL DEFAULT '0' COMMENT '页面禁止发送给好友',
  `article_keyword` varchar(255) NOT NULL DEFAULT '' COMMENT '页面关键字',
  `article_report` int(1) NOT NULL DEFAULT '0' COMMENT '举报按钮',
  `product_advs_type` int(1) NOT NULL DEFAULT '0' COMMENT '营销显示产品',
  `product_advs_title` varchar(255) NOT NULL DEFAULT '' COMMENT '营销产品标题',
  `product_advs_more` varchar(255) NOT NULL DEFAULT '' COMMENT '推广产品底部标题',
  `product_advs_link` varchar(255) NOT NULL DEFAULT '' COMMENT '推广产品底部链接',
  `product_advs` text NOT NULL COMMENT '营销商品',
  `article_state` int(1) NOT NULL DEFAULT '0',
  `network_attachment` varchar(255) DEFAULT '',
  `uniacid` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='营销文章';

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_article_category`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_article_category` (
  `id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL DEFAULT '' COMMENT '分类名称',
  `uniacid` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='营销表单分类';

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_article_log`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_article_log` (
  `id` int(11) NOT NULL,
  `aid` int(11) NOT NULL DEFAULT '0' COMMENT '文章id',
  `read` int(11) NOT NULL DEFAULT '0',
  `like` int(11) NOT NULL DEFAULT '0',
  `openid` varchar(255) NOT NULL DEFAULT '' COMMENT '用户openid',
  `uniacid` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='点赞/阅读记录';

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_article_report`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_article_report` (
  `id` int(11) NOT NULL,
  `mid` int(11) NOT NULL DEFAULT '0',
  `openid` varchar(255) NOT NULL DEFAULT '',
  `aid` int(11) DEFAULT '0',
  `cate` varchar(255) NOT NULL DEFAULT '',
  `cons` varchar(255) NOT NULL DEFAULT '',
  `uniacid` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户举报记录';

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_article_share`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_article_share` (
  `id` int(11) NOT NULL,
  `aid` int(11) NOT NULL DEFAULT '0',
  `share_user` int(11) NOT NULL DEFAULT '0' COMMENT '分享人',
  `click_user` int(11) NOT NULL DEFAULT '0' COMMENT '点击人',
  `click_date` varchar(20) NOT NULL DEFAULT '' COMMENT '执行时间',
  `add_credit` int(11) NOT NULL DEFAULT '0' COMMENT '添加的积分',
  `add_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '添加的余额',
  `uniacid` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户分享数据';

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_article_sys`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_article_sys` (
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `article_message` varchar(255) NOT NULL DEFAULT '',
  `article_title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `article_image` varchar(300) NOT NULL DEFAULT '' COMMENT '图片',
  `article_shownum` int(11) NOT NULL DEFAULT '0' COMMENT '每页数量',
  `article_keyword` varchar(255) NOT NULL DEFAULT '' COMMENT '关键字',
  `article_temp` int(11) NOT NULL DEFAULT '0',
  `article_area` text COMMENT '文章阅读地区'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文章设置';

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_bonus`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_bonus` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `send_bonus_sn` int(11) DEFAULT '0',
  `money` decimal(10,2) DEFAULT '0.00',
  `total` int(11) DEFAULT '0',
  `status` tinyint(1) DEFAULT '0',
  `type` tinyint(1) DEFAULT '0' COMMENT '0 手动 1 自动',
  `paymethod` tinyint(1) DEFAULT '0',
  `isglobal` tinyint(1) DEFAULT '0',
  `sendpay_error` tinyint(1) DEFAULT '0',
  `utime` int(11) DEFAULT '0',
  `ctime` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='分红明细';

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_bonus_goods`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_bonus_goods` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `ordergoodid` int(11) DEFAULT '0',
  `orderid` int(11) DEFAULT '0',
  `total` int(11) DEFAULT '0',
  `optionname` varchar(100) DEFAULT '',
  `mid` int(11) DEFAULT '0' COMMENT '所有人，分佣者',
  `levelid` int(11) DEFAULT '0' COMMENT '级别id',
  `level` int(11) DEFAULT '0' COMMENT '1/2/3哪一级',
  `money` decimal(10,2) DEFAULT '0.00' COMMENT '应得佣金',
  `status` tinyint(3) DEFAULT '0' COMMENT '申请状态，-2删除，-1无效，0未申请，1申请，2审核通过 3已打款',
  `content` text,
  `applytime` int(11) DEFAULT '0',
  `checktime` int(11) DEFAULT '0',
  `paytime` int(11) DEFAULT '0',
  `invalidtime` int(11) DEFAULT '0',
  `deletetime` int(11) DEFAULT '0',
  `createtime` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='分红单商品表';

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_bonus_level`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_bonus_level` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `levelname` varchar(50) DEFAULT '',
  `agent_money` decimal(10,2) DEFAULT '0.00',
  `pcommission` decimal(10,2) DEFAULT '0.00',
  `commissionmoney` decimal(10,2) DEFAULT '0.00',
  `ordermoney` decimal(10,2) DEFAULT '0.00',
  `downcount` int(10) DEFAULT '0',
  `ordercount` int(10) DEFAULT '0',
  `downcountlevel1` int(10) DEFAULT '0',
  `type` int(11) DEFAULT '0' COMMENT '1为区域代理',
  `level` int(10) DEFAULT '0' COMMENT '等级权重',
  `premier` tinyint(1) DEFAULT '0' COMMENT '0 普通级别 1 最高级别',
  `content` text COMMENT '微信消息提醒追加内容'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='分红代理等级表';

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_bonus_log`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_bonus_log` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT '',
  `uid` int(11) DEFAULT '0',
  `money` decimal(10,2) DEFAULT '0.00',
  `logno` varchar(255) DEFAULT '',
  `send_bonus_sn` int(11) DEFAULT '0',
  `paymethod` tinyint(1) DEFAULT '0',
  `isglobal` tinyint(1) DEFAULT '0',
  `status` tinyint(1) DEFAULT '0',
  `sendpay` tinyint(1) DEFAULT '0',
  `ctime` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='分红日志';

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_carrier`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_carrier` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `realname` varchar(50) DEFAULT '',
  `mobile` varchar(50) DEFAULT '',
  `address` varchar(255) DEFAULT '',
  `deleted` tinyint(1) DEFAULT '0',
  `createtime` int(11) DEFAULT '0',
  `displayorder` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_category`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_category` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0' COMMENT '所属帐号',
  `name` varchar(50) DEFAULT NULL COMMENT '分类名称',
  `thumb` varchar(255) DEFAULT NULL COMMENT '分类图片',
  `parentid` int(11) DEFAULT '0' COMMENT '上级分类ID,0为第一级',
  `isrecommand` int(10) DEFAULT '0',
  `description` varchar(500) DEFAULT NULL COMMENT '分类介绍',
  `displayorder` tinyint(3) unsigned DEFAULT '0' COMMENT '排序',
  `enabled` tinyint(1) DEFAULT '1' COMMENT '是否开启',
  `ishome` tinyint(3) DEFAULT '0',
  `advimg` varchar(255) DEFAULT '',
  `advurl` varchar(500) DEFAULT '',
  `level` tinyint(3) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_commission_apply`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_commission_apply` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `applyno` varchar(255) DEFAULT '',
  `mid` int(11) DEFAULT '0' COMMENT '会员ID',
  `type` tinyint(3) DEFAULT '0' COMMENT '0 余额 1 微信',
  `orderids` text,
  `commission` decimal(10,2) DEFAULT '0.00',
  `commission_pay` decimal(10,2) DEFAULT '0.00',
  `content` text,
  `status` tinyint(3) DEFAULT '0' COMMENT '-1 无效 0 未知 1 正在申请 2 审核通过 3 已经打款',
  `applytime` int(11) DEFAULT '0',
  `checktime` int(11) DEFAULT '0',
  `paytime` int(11) DEFAULT '0',
  `invalidtime` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_commission_clickcount`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_commission_clickcount` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT '',
  `from_openid` varchar(255) DEFAULT '',
  `clicktime` int(11) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_commission_level`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_commission_level` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `levelname` varchar(50) DEFAULT '',
  `commission1` decimal(10,2) DEFAULT '0.00',
  `commission2` decimal(10,2) DEFAULT '0.00',
  `commission3` decimal(10,2) DEFAULT '0.00',
  `commissionmoney` decimal(10,2) DEFAULT '0.00',
  `ordermoney` decimal(10,2) DEFAULT '0.00',
  `downcount` int(11) DEFAULT '0',
  `ordercount` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_commission_log`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_commission_log` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `applyid` int(11) DEFAULT '0',
  `mid` int(11) DEFAULT '0',
  `commission` decimal(10,2) DEFAULT '0.00',
  `createtime` int(11) DEFAULT '0',
  `commission_pay` decimal(10,2) DEFAULT '0.00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_commission_shop`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_commission_shop` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `mid` int(11) DEFAULT '0',
  `name` varchar(255) DEFAULT '',
  `logo` varchar(255) DEFAULT '',
  `img` varchar(255) DEFAULT NULL,
  `desc` varchar(255) DEFAULT '',
  `selectgoods` tinyint(3) DEFAULT '0',
  `selectcategory` tinyint(3) DEFAULT '0',
  `goodsids` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_coupon`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_coupon` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `catid` int(11) DEFAULT '0',
  `couponname` varchar(255) DEFAULT '',
  `gettype` tinyint(3) DEFAULT '0',
  `getmax` int(11) DEFAULT '0',
  `usetype` tinyint(3) DEFAULT '0' COMMENT '消费方式 0 付款使用 1 下单使用',
  `returntype` tinyint(3) DEFAULT '0' COMMENT '退回方式 0 不可退回 1 取消订单(未付款) 2.退款可以退回',
  `bgcolor` varchar(255) DEFAULT '',
  `enough` decimal(10,2) DEFAULT '0.00',
  `timelimit` tinyint(3) DEFAULT '0' COMMENT '0 领取后几天有效 1 时间范围',
  `coupontype` tinyint(3) DEFAULT '0' COMMENT '0 优惠券 1 充值券',
  `timedays` int(11) DEFAULT '0',
  `timestart` int(11) DEFAULT '0',
  `timeend` int(11) DEFAULT '0',
  `discount` decimal(10,2) DEFAULT '0.00' COMMENT '折扣',
  `deduct` decimal(10,2) DEFAULT '0.00' COMMENT '抵扣',
  `backtype` tinyint(3) DEFAULT '0',
  `backmoney` varchar(50) DEFAULT '' COMMENT '返现',
  `backcredit` varchar(50) DEFAULT '' COMMENT '返积分',
  `backredpack` varchar(50) DEFAULT '',
  `backwhen` tinyint(3) DEFAULT '0',
  `thumb` varchar(255) DEFAULT '',
  `desc` text,
  `createtime` int(11) DEFAULT '0',
  `total` int(11) DEFAULT '0' COMMENT '数量 -1 不限制',
  `status` tinyint(3) DEFAULT '0' COMMENT '可用',
  `money` decimal(10,2) DEFAULT '0.00' COMMENT '购买价格',
  `respdesc` text COMMENT '推送描述',
  `respthumb` varchar(255) DEFAULT '' COMMENT '推送图片',
  `resptitle` varchar(255) DEFAULT '' COMMENT '推送标题',
  `respurl` varchar(255) DEFAULT '',
  `credit` int(11) DEFAULT '0',
  `usecredit2` tinyint(3) DEFAULT '0',
  `remark` varchar(1000) DEFAULT '',
  `descnoset` tinyint(3) DEFAULT '0',
  `pwdkey` varchar(255) DEFAULT '',
  `pwdsuc` text,
  `pwdfail` text,
  `pwdurl` varchar(255) DEFAULT '',
  `pwdask` text,
  `pwdstatus` tinyint(3) DEFAULT '0',
  `pwdtimes` int(11) DEFAULT '0',
  `pwdfull` text,
  `pwdwords` text,
  `pwdopen` tinyint(3) DEFAULT '0',
  `pwdown` text,
  `pwdexit` varchar(255) DEFAULT '',
  `pwdexitstr` text,
  `displayorder` int(11) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_coupon_category`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_coupon_category` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `name` varchar(255) DEFAULT '',
  `displayorder` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_coupon_data`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_coupon_data` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT '',
  `couponid` int(11) DEFAULT '0',
  `gettype` tinyint(3) DEFAULT '0' COMMENT '获取方式 0 发放 1 领取 2 积分商城',
  `used` int(11) DEFAULT '0',
  `usetime` int(11) DEFAULT '0',
  `gettime` int(11) DEFAULT '0' COMMENT '获取时间',
  `senduid` int(11) DEFAULT '0',
  `ordersn` varchar(255) DEFAULT '',
  `back` tinyint(3) DEFAULT '0',
  `backtime` int(11) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_coupon_guess`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_coupon_guess` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `couponid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT '',
  `times` int(11) DEFAULT '0',
  `pwdkey` varchar(255) DEFAULT '',
  `ok` tinyint(3) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_coupon_log`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_coupon_log` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `logno` varchar(255) DEFAULT '',
  `openid` varchar(255) DEFAULT '',
  `couponid` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '0',
  `paystatus` tinyint(3) DEFAULT '0',
  `creditstatus` tinyint(3) DEFAULT '0',
  `createtime` int(11) DEFAULT '0',
  `paytype` tinyint(3) DEFAULT '0',
  `getfrom` tinyint(3) DEFAULT '0' COMMENT '0 发放 1 中心 2 积分兑换'
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_creditshop_adv`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_creditshop_adv` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `advname` varchar(50) DEFAULT '',
  `link` varchar(255) DEFAULT '',
  `thumb` varchar(255) DEFAULT '',
  `displayorder` int(11) DEFAULT '0',
  `enabled` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_creditshop_category`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_creditshop_category` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0' COMMENT '所属帐号',
  `name` varchar(50) DEFAULT NULL COMMENT '分类名称',
  `thumb` varchar(255) DEFAULT NULL COMMENT '分类图片',
  `displayorder` tinyint(3) unsigned DEFAULT '0' COMMENT '排序',
  `enabled` tinyint(1) DEFAULT '1' COMMENT '是否开启',
  `advimg` varchar(255) DEFAULT '',
  `advurl` varchar(500) DEFAULT '',
  `isrecommand` tinyint(3) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_creditshop_goods`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_creditshop_goods` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `displayorder` int(11) DEFAULT '0',
  `title` varchar(255) DEFAULT '',
  `cate` int(11) DEFAULT '0',
  `thumb` varchar(255) DEFAULT '',
  `price` decimal(10,2) DEFAULT '0.00',
  `type` tinyint(3) DEFAULT '0',
  `credit` int(11) DEFAULT '0',
  `money` decimal(10,2) DEFAULT '0.00',
  `total` int(11) DEFAULT '0',
  `totalday` int(11) DEFAULT '0',
  `chance` int(11) DEFAULT '0',
  `chanceday` int(11) DEFAULT '0',
  `detail` text,
  `rate1` int(11) DEFAULT '0',
  `rate2` int(11) DEFAULT '0',
  `endtime` int(11) DEFAULT '0',
  `joins` int(11) DEFAULT '0',
  `views` int(11) DEFAULT '0',
  `createtime` int(11) DEFAULT '0',
  `status` tinyint(3) DEFAULT '0',
  `deleted` tinyint(3) DEFAULT '0',
  `showlevels` text,
  `buylevels` text,
  `showgroups` text,
  `buygroups` text,
  `vip` tinyint(3) DEFAULT '0',
  `istop` tinyint(3) DEFAULT '0',
  `isrecommand` tinyint(3) DEFAULT '0',
  `istime` tinyint(3) DEFAULT '0',
  `timestart` int(11) DEFAULT '0',
  `timeend` int(11) DEFAULT '0',
  `share_title` varchar(255) DEFAULT '',
  `share_icon` varchar(255) DEFAULT '',
  `share_desc` varchar(500) DEFAULT '',
  `followneed` tinyint(3) DEFAULT '0',
  `followtext` varchar(255) DEFAULT '',
  `subtitle` varchar(255) DEFAULT '',
  `subdetail` text,
  `noticedetail` text,
  `usedetail` varchar(255) DEFAULT '',
  `goodsdetail` text,
  `isendtime` tinyint(3) DEFAULT '0',
  `usecredit2` tinyint(3) DEFAULT '0',
  `area` varchar(255) DEFAULT '',
  `dispatch` decimal(10,2) DEFAULT '0.00',
  `storeids` text,
  `noticeopenid` varchar(255) DEFAULT '',
  `noticetype` tinyint(3) DEFAULT '0',
  `isverify` tinyint(3) DEFAULT '0',
  `goodstype` tinyint(3) DEFAULT '0',
  `couponid` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_creditshop_log`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_creditshop_log` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `logno` varchar(255) DEFAULT '',
  `eno` varchar(255) DEFAULT '' COMMENT '兑换码',
  `openid` varchar(255) DEFAULT '',
  `goodsid` int(11) DEFAULT '0',
  `createtime` int(11) DEFAULT '0',
  `status` tinyint(3) DEFAULT '0' COMMENT '0 只生成记录未参加 1 未中奖 2 已中奖 3 已发奖',
  `paystatus` tinyint(3) DEFAULT '0' COMMENT '支付状态 -1 不需要支付 0 未支付 1 已支付',
  `paytype` tinyint(3) DEFAULT '-1' COMMENT '支付类型 -1 不需要支付 0 余额 1 微信',
  `dispatchstatus` tinyint(3) DEFAULT '0' COMMENT '运费状态 -1 不需要运费 0 未支付 1 已支付',
  `creditpay` tinyint(3) DEFAULT '0' COMMENT '积分支付 0 未支付 1 已支付',
  `addressid` int(11) DEFAULT '0' COMMENT '收货地址',
  `dispatchno` varchar(255) DEFAULT '' COMMENT '运费支付单号',
  `usetime` int(11) DEFAULT '0',
  `express` varchar(255) DEFAULT '',
  `expresssn` varchar(255) DEFAULT '',
  `expresscom` varchar(255) DEFAULT '',
  `verifyopenid` varchar(255) DEFAULT '',
  `storeid` int(11) DEFAULT '0',
  `realname` varchar(255) DEFAULT '',
  `mobile` varchar(255) DEFAULT '',
  `couponid` int(11) DEFAULT '0',
  `dupdate1` tinyint(3) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_designer`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_designer` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL DEFAULT '0' COMMENT '公众号',
  `pagename` varchar(255) NOT NULL DEFAULT '' COMMENT '页面名称',
  `pagetype` tinyint(3) NOT NULL DEFAULT '0' COMMENT '页面类型',
  `pageinfo` text NOT NULL,
  `createtime` varchar(255) NOT NULL DEFAULT '' COMMENT '页面创建时间',
  `keyword` varchar(255) DEFAULT '',
  `savetime` varchar(255) NOT NULL DEFAULT '' COMMENT '页面最后保存时间',
  `setdefault` tinyint(3) NOT NULL DEFAULT '0' COMMENT '默认页面',
  `datas` text NOT NULL COMMENT '数据'
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_designer_menu`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_designer_menu` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `menuname` varchar(255) DEFAULT '',
  `isdefault` tinyint(3) DEFAULT '0',
  `createtime` int(11) DEFAULT '0',
  `menus` text,
  `params` text
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_dispatch`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_dispatch` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `dispatchname` varchar(50) DEFAULT '',
  `dispatchtype` int(11) DEFAULT '0',
  `displayorder` int(11) DEFAULT '0',
  `firstprice` decimal(10,2) DEFAULT '0.00',
  `secondprice` decimal(10,2) DEFAULT '0.00',
  `firstweight` int(11) DEFAULT '0',
  `secondweight` int(11) DEFAULT '0',
  `express` varchar(250) DEFAULT '',
  `areas` text,
  `carriers` text,
  `enabled` int(11) DEFAULT '0',
  `isdefault` tinyint(1) DEFAULT '0',
  `calculatetype` tinyint(1) DEFAULT '0',
  `firstnumprice` decimal(10,2) DEFAULT '0.00',
  `secondnumprice` decimal(10,2) DEFAULT '0.00',
  `firstnum` int(11) DEFAULT '0',
  `secondnum` int(11) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_diyform_category`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_diyform_category` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0' COMMENT '所属帐号',
  `name` varchar(50) DEFAULT NULL COMMENT '分类名称'
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_diyform_data`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_diyform_data` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `typeid` int(11) NOT NULL DEFAULT '0' COMMENT '类型id',
  `cid` int(11) DEFAULT '0' COMMENT '关联id',
  `diyformfields` text,
  `fields` text NOT NULL COMMENT '字符集',
  `openid` varchar(255) NOT NULL DEFAULT '' COMMENT '使用者openid',
  `type` tinyint(2) DEFAULT '0' COMMENT '该数据所属模块'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_diyform_temp`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_diyform_temp` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `typeid` int(11) DEFAULT '0',
  `cid` int(11) NOT NULL DEFAULT '0' COMMENT '关联id',
  `diyformfields` text,
  `fields` text NOT NULL COMMENT '字符集',
  `openid` varchar(255) NOT NULL DEFAULT '' COMMENT '使用者openid',
  `type` tinyint(1) DEFAULT '0' COMMENT '类型',
  `diyformid` int(11) DEFAULT '0',
  `diyformdata` text
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_diyform_type`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_diyform_type` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `cate` int(11) DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '分类名称',
  `fields` text NOT NULL COMMENT '字段集',
  `usedata` int(11) NOT NULL DEFAULT '0' COMMENT '已用数据',
  `alldata` int(11) NOT NULL DEFAULT '0' COMMENT '全部数据',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态'
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_exhelper_express`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_exhelper_express` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `type` int(1) NOT NULL DEFAULT '1' COMMENT '单据分类 1为快递单 2为发货单',
  `expressname` varchar(255) DEFAULT '',
  `expresscom` varchar(255) NOT NULL DEFAULT '',
  `express` varchar(255) NOT NULL DEFAULT '',
  `width` decimal(10,2) DEFAULT '0.00',
  `datas` text,
  `height` decimal(10,2) DEFAULT '0.00',
  `bg` varchar(255) DEFAULT '',
  `isdefault` tinyint(3) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_exhelper_senduser`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_exhelper_senduser` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `sendername` varchar(255) DEFAULT '' COMMENT '发件人',
  `sendertel` varchar(255) DEFAULT '' COMMENT '发件人联系电话',
  `sendersign` varchar(255) DEFAULT '' COMMENT '发件人签名',
  `sendercode` int(11) DEFAULT NULL COMMENT '发件地址邮编',
  `senderaddress` varchar(255) DEFAULT '' COMMENT '发件地址',
  `sendercity` varchar(255) DEFAULT NULL COMMENT '发件城市',
  `isdefault` tinyint(3) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_exhelper_sys`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_exhelper_sys` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(20) NOT NULL DEFAULT 'localhost',
  `port` int(11) NOT NULL DEFAULT '8000'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_express`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_express` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `express_name` varchar(50) DEFAULT '',
  `displayorder` int(11) DEFAULT '0',
  `express_price` varchar(10) DEFAULT '',
  `express_area` varchar(100) DEFAULT '',
  `express_url` varchar(255) DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_feedback`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_feedback` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(50) DEFAULT '0',
  `type` tinyint(1) DEFAULT '1' COMMENT '1为维权，2为投诉',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态 0 未解决，1用户同意，2用户拒绝',
  `feedbackid` varchar(100) DEFAULT '' COMMENT '投诉单号',
  `transid` varchar(100) DEFAULT '' COMMENT '订单号',
  `reason` varchar(1000) DEFAULT '' COMMENT '理由',
  `solution` varchar(1000) DEFAULT '' COMMENT '期待解决方案',
  `remark` varchar(1000) DEFAULT '' COMMENT '备注',
  `createtime` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_goods`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_goods` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `pcate` int(11) DEFAULT '0',
  `ccate` int(11) DEFAULT '0',
  `type` tinyint(1) DEFAULT '1' COMMENT '1为实体，2为虚拟',
  `status` tinyint(1) DEFAULT '1',
  `displayorder` int(11) DEFAULT '0',
  `title` varchar(100) DEFAULT '',
  `thumb` varchar(255) DEFAULT '',
  `unit` varchar(5) DEFAULT '',
  `description` varchar(1000) DEFAULT '',
  `content` text,
  `goodssn` varchar(50) DEFAULT '',
  `productsn` varchar(50) DEFAULT '',
  `productprice` decimal(10,2) DEFAULT '0.00',
  `marketprice` decimal(10,2) DEFAULT '0.00',
  `costprice` decimal(10,2) DEFAULT '0.00',
  `bonusmoney` decimal(10,2) DEFAULT NULL,
  `originalprice` decimal(10,2) DEFAULT '0.00' COMMENT '原价',
  `total` int(10) DEFAULT '0',
  `totalcnf` int(11) DEFAULT '0' COMMENT '0 拍下减库存 1 付款减库存 2 永久不减',
  `sales` int(11) DEFAULT '0',
  `salesreal` int(11) DEFAULT '0',
  `spec` varchar(5000) DEFAULT '',
  `createtime` int(11) DEFAULT '0',
  `weight` decimal(10,2) DEFAULT '0.00',
  `credit` varchar(255) DEFAULT NULL,
  `maxbuy` int(11) DEFAULT '0',
  `usermaxbuy` int(11) DEFAULT '0',
  `hasoption` int(11) DEFAULT '0',
  `dispatch` int(11) DEFAULT '0',
  `thumb_url` text,
  `isnew` tinyint(1) DEFAULT '0',
  `ishot` tinyint(1) DEFAULT '0',
  `isdiscount` tinyint(1) DEFAULT '0',
  `isrecommand` tinyint(1) DEFAULT '0',
  `issendfree` tinyint(1) DEFAULT '0',
  `istime` tinyint(1) DEFAULT '0',
  `iscomment` tinyint(1) DEFAULT '0',
  `timestart` int(11) DEFAULT '0',
  `timeend` int(11) DEFAULT '0',
  `viewcount` int(11) DEFAULT '0',
  `deleted` tinyint(3) DEFAULT '0',
  `hascommission` tinyint(3) DEFAULT '0',
  `commission1_rate` decimal(10,2) DEFAULT '0.00',
  `commission1_pay` decimal(10,2) DEFAULT '0.00',
  `commission2_rate` decimal(10,2) DEFAULT '0.00',
  `commission2_pay` decimal(10,2) DEFAULT '0.00',
  `commission3_rate` decimal(10,2) DEFAULT '0.00',
  `commission3_pay` decimal(10,2) DEFAULT '0.00',
  `score` decimal(10,2) DEFAULT '0.00',
  `taobaoid` varchar(255) DEFAULT '',
  `taotaoid` varchar(255) DEFAULT '',
  `taobaourl` varchar(255) DEFAULT '',
  `updatetime` int(11) DEFAULT '0',
  `share_title` varchar(255) DEFAULT '',
  `share_icon` varchar(255) DEFAULT '',
  `cash` tinyint(3) DEFAULT '0',
  `commission_thumb` varchar(255) DEFAULT '',
  `isnodiscount` tinyint(3) DEFAULT '0',
  `showlevels` text,
  `buylevels` text,
  `showgroups` text,
  `buygroups` text,
  `isverify` tinyint(3) DEFAULT '0',
  `storeids` text,
  `noticeopenid` text,
  `tcate` int(11) DEFAULT '0',
  `noticetype` text,
  `needfollow` tinyint(3) DEFAULT '0',
  `followtip` varchar(255) DEFAULT '',
  `followurl` varchar(255) DEFAULT '',
  `deduct` decimal(10,2) DEFAULT '0.00',
  `virtual` int(11) DEFAULT '0',
  `ccates` text,
  `discounts` text,
  `nocommission` tinyint(3) DEFAULT '0',
  `hidecommission` tinyint(3) DEFAULT '0',
  `pcates` text,
  `tcates` text,
  `artid` int(11) DEFAULT '0',
  `detail_logo` varchar(255) DEFAULT '',
  `detail_shopname` varchar(255) DEFAULT '',
  `detail_btntext1` varchar(255) DEFAULT '',
  `detail_btnurl1` varchar(255) DEFAULT '',
  `detail_btntext2` varchar(255) DEFAULT '',
  `detail_btnurl2` varchar(255) DEFAULT '',
  `detail_totaltitle` varchar(255) DEFAULT '',
  `deduct2` decimal(10,2) DEFAULT '0.00',
  `ednum` int(11) DEFAULT '0',
  `edmoney` decimal(10,2) DEFAULT '0.00',
  `edareas` text,
  `cates` text,
  `shorttitle` varchar(500) DEFAULT NULL,
  `supplier_uid` int(11) NOT NULL COMMENT '供应商ID',
  `diyformtype` tinyint(3) DEFAULT '0',
  `manydeduct` tinyint(1) DEFAULT '0',
  `dispatchtype` tinyint(1) DEFAULT '0',
  `dispatchid` int(11) DEFAULT '0',
  `dispatchprice` decimal(10,2) DEFAULT '0.00',
  `diyformid` int(11) DEFAULT '0',
  `diymode` tinyint(3) DEFAULT '0',
  `commission_level_id` int(11) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_goods_comment`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_goods_comment` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `goodsid` int(10) DEFAULT '0',
  `openid` varchar(50) DEFAULT '',
  `nickname` varchar(50) DEFAULT '',
  `headimgurl` varchar(255) DEFAULT '',
  `content` varchar(255) DEFAULT '',
  `createtime` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_goods_option`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_goods_option` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `goodsid` int(10) DEFAULT '0',
  `title` varchar(50) DEFAULT '',
  `thumb` varchar(60) DEFAULT '',
  `productprice` decimal(10,2) DEFAULT '0.00',
  `marketprice` decimal(10,2) DEFAULT '0.00',
  `costprice` decimal(10,2) DEFAULT '0.00',
  `stock` int(11) DEFAULT '0',
  `weight` decimal(10,2) DEFAULT '0.00',
  `displayorder` int(11) DEFAULT '0',
  `specs` text,
  `skuId` varchar(255) DEFAULT '',
  `goodssn` varchar(255) DEFAULT '',
  `productsn` varchar(255) DEFAULT '',
  `virtual` int(11) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_goods_param`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_goods_param` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `goodsid` int(10) DEFAULT '0',
  `title` varchar(50) DEFAULT '',
  `value` text,
  `displayorder` int(11) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_goods_spec`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_goods_spec` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `goodsid` int(11) DEFAULT '0',
  `title` varchar(50) DEFAULT '',
  `description` varchar(1000) DEFAULT '',
  `displaytype` tinyint(3) DEFAULT '0',
  `content` text,
  `displayorder` int(11) DEFAULT '0',
  `propId` varchar(255) DEFAULT ''
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_goods_spec_item`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_goods_spec_item` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `specid` int(11) DEFAULT '0',
  `title` varchar(255) DEFAULT '',
  `thumb` varchar(255) DEFAULT '',
  `show` int(11) DEFAULT '0',
  `displayorder` int(11) DEFAULT '0',
  `valueId` varchar(255) DEFAULT '',
  `virtual` int(11) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_member`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_member` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `uid` int(11) DEFAULT '0',
  `groupid` int(11) DEFAULT '0',
  `level` int(11) DEFAULT '0',
  `agentid` int(11) DEFAULT '0',
  `openid` varchar(50) DEFAULT '',
  `realname` varchar(20) DEFAULT '',
  `mobile` varchar(11) DEFAULT '',
  `pwd` varchar(100) DEFAULT NULL,
  `weixin` varchar(100) DEFAULT '',
  `content` text,
  `createtime` int(10) DEFAULT '0',
  `agenttime` int(10) DEFAULT '0',
  `status` tinyint(1) DEFAULT '0',
  `isagent` tinyint(1) DEFAULT '0',
  `clickcount` int(11) DEFAULT '0',
  `agentlevel` int(11) DEFAULT '0',
  `bonuslevel` int(11) DEFAULT '0',
  `bonus_status` tinyint(1) DEFAULT '0',
  `noticeset` text,
  `nickname` varchar(255) DEFAULT '',
  `credit1` int(11) DEFAULT '0',
  `credit2` decimal(10,2) DEFAULT '0.00',
  `birthyear` varchar(255) DEFAULT '',
  `birthmonth` varchar(255) DEFAULT '',
  `birthday` varchar(255) DEFAULT '',
  `gender` tinyint(3) DEFAULT '0',
  `avatar` varchar(255) DEFAULT '',
  `province` varchar(255) DEFAULT '',
  `city` varchar(255) DEFAULT '',
  `area` varchar(255) DEFAULT '',
  `childtime` int(11) DEFAULT '0',
  `inviter` int(11) DEFAULT '0',
  `agentnotupgrade` tinyint(3) DEFAULT '0',
  `agentselectgoods` tinyint(3) DEFAULT '0',
  `agentblack` tinyint(3) DEFAULT '0',
  `fixagentid` tinyint(3) DEFAULT '0',
  `regtype` tinyint(3) DEFAULT '1',
  `isbindmobile` tinyint(3) DEFAULT '0',
  `isjumpbind` tinyint(3) DEFAULT '0',
  `diymemberid` int(11) DEFAULT '0',
  `diymemberdataid` int(11) DEFAULT '0',
  `diycommissionid` int(11) DEFAULT '0',
  `diycommissiondataid` int(11) DEFAULT '0',
  `diymemberfields` text,
  `diymemberdata` text,
  `diycommissionfields` text,
  `diycommissiondata` text,
  `isblack` tinyint(3) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_member_address`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_member_address` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(50) DEFAULT '0',
  `realname` varchar(20) DEFAULT '',
  `mobile` varchar(11) DEFAULT '',
  `province` varchar(30) DEFAULT '',
  `city` varchar(30) DEFAULT '',
  `area` varchar(30) DEFAULT '',
  `address` varchar(300) DEFAULT '',
  `isdefault` tinyint(1) DEFAULT '0',
  `zipcode` varchar(255) DEFAULT '',
  `deleted` tinyint(1) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_member_cart`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_member_cart` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(100) DEFAULT '',
  `goodsid` int(11) DEFAULT '0',
  `total` int(11) DEFAULT '0',
  `marketprice` decimal(10,2) DEFAULT '0.00',
  `deleted` tinyint(1) DEFAULT '0',
  `optionid` int(11) DEFAULT '0',
  `createtime` int(11) DEFAULT '0',
  `diyformdata` text,
  `diyformfields` text,
  `diyformdataid` int(11) DEFAULT '0',
  `diyformid` int(11) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_member_favorite`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_member_favorite` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `goodsid` int(10) DEFAULT '0',
  `openid` varchar(50) DEFAULT '',
  `deleted` tinyint(1) DEFAULT '0',
  `createtime` int(11) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_member_group`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_member_group` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `groupname` varchar(255) DEFAULT ''
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_member_history`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_member_history` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `goodsid` int(10) DEFAULT '0',
  `openid` varchar(50) DEFAULT '',
  `deleted` tinyint(1) DEFAULT '0',
  `createtime` int(11) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=244 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_member_level`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_member_level` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `level` int(11) DEFAULT '0',
  `levelname` varchar(50) DEFAULT '',
  `ordermoney` decimal(10,2) DEFAULT '0.00',
  `ordercount` int(10) DEFAULT '0',
  `discount` decimal(10,2) DEFAULT '0.00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_member_log`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_member_log` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT '',
  `type` tinyint(3) DEFAULT NULL COMMENT '0 充值 1 提现',
  `logno` varchar(255) DEFAULT '',
  `title` varchar(255) DEFAULT '',
  `createtime` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '0' COMMENT '0 生成 1 成功 2 失败',
  `money` decimal(10,2) DEFAULT '0.00',
  `rechargetype` varchar(255) DEFAULT '' COMMENT '充值类型',
  `gives` decimal(10,2) DEFAULT NULL,
  `couponid` int(11) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_member_message_template`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_member_message_template` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `title` varchar(255) DEFAULT '',
  `template_id` varchar(255) DEFAULT '',
  `first` text NOT NULL COMMENT '键名',
  `firstcolor` varchar(255) DEFAULT '',
  `data` text NOT NULL COMMENT '颜色',
  `remark` text NOT NULL COMMENT '键值',
  `remarkcolor` varchar(255) DEFAULT '',
  `url` varchar(255) NOT NULL,
  `createtime` int(11) DEFAULT '0',
  `sendtimes` int(11) DEFAULT '0',
  `sendcount` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_notice`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_notice` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `displayorder` int(11) DEFAULT '0',
  `title` varchar(255) DEFAULT '',
  `thumb` varchar(255) DEFAULT '',
  `link` varchar(255) DEFAULT '',
  `detail` text,
  `status` tinyint(3) DEFAULT '0',
  `createtime` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_order`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_order` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(50) DEFAULT '',
  `agentid` int(11) DEFAULT '0',
  `ordersn` varchar(20) DEFAULT '',
  `price` decimal(10,2) DEFAULT '0.00',
  `goodsprice` decimal(10,2) DEFAULT '0.00',
  `discountprice` decimal(10,2) DEFAULT '0.00',
  `status` tinyint(4) DEFAULT '0' COMMENT '-1取消状态，0普通状态，1为已付款，2为已发货，3为成功',
  `paytype` tinyint(1) DEFAULT '0' COMMENT '1为余额，2为在线，3为到付',
  `transid` varchar(30) DEFAULT '0' COMMENT '微信支付单号',
  `remark` varchar(1000) DEFAULT '',
  `addressid` int(11) DEFAULT '0',
  `dispatchprice` decimal(10,2) DEFAULT '0.00',
  `dispatchid` int(10) DEFAULT '0',
  `createtime` int(10) DEFAULT NULL,
  `dispatchtype` tinyint(3) DEFAULT '0',
  `carrier` text,
  `refundid` int(11) DEFAULT '0',
  `iscomment` tinyint(3) DEFAULT '0',
  `creditadd` tinyint(3) DEFAULT '0',
  `deleted` tinyint(3) DEFAULT '0',
  `userdeleted` tinyint(3) DEFAULT '0',
  `finishtime` int(11) DEFAULT '0',
  `paytime` int(11) DEFAULT '0',
  `expresscom` varchar(30) NOT NULL DEFAULT '',
  `expresssn` varchar(50) NOT NULL DEFAULT '',
  `express` varchar(255) DEFAULT '',
  `sendtime` int(11) DEFAULT '0',
  `fetchtime` int(11) DEFAULT '0',
  `cash` tinyint(3) DEFAULT '0',
  `canceltime` int(11) DEFAULT NULL,
  `cancelpaytime` int(11) DEFAULT '0',
  `refundtime` int(11) DEFAULT '0',
  `isverify` tinyint(3) DEFAULT '0',
  `verified` tinyint(3) DEFAULT '0',
  `verifyopenid` varchar(255) DEFAULT '',
  `verifycode` text,
  `verifytime` int(11) DEFAULT '0',
  `verifystoreid` int(11) DEFAULT '0',
  `deductprice` decimal(10,2) DEFAULT '0.00',
  `deductcredit` int(11) DEFAULT '0',
  `deductcredit2` decimal(10,2) DEFAULT '0.00',
  `deductenough` decimal(10,2) DEFAULT '0.00',
  `virtual` int(11) DEFAULT '0',
  `virtual_info` text,
  `virtual_str` text,
  `address` text,
  `sysdeleted` tinyint(3) DEFAULT '0',
  `ordersn2` int(11) DEFAULT '0',
  `changeprice` decimal(10,2) DEFAULT '0.00',
  `changedispatchprice` decimal(10,2) DEFAULT '0.00',
  `oldprice` decimal(10,2) DEFAULT '0.00',
  `olddispatchprice` decimal(10,2) DEFAULT '0.00',
  `isvirtual` tinyint(3) DEFAULT '0',
  `couponid` int(11) DEFAULT '0',
  `couponprice` decimal(10,2) DEFAULT '0.00',
  `supplier_uid` int(11) NOT NULL COMMENT '供应商ID',
  `printstate` tinyint(3) DEFAULT '0',
  `printstate2` tinyint(3) DEFAULT '0',
  `diyformid` int(11) DEFAULT '0',
  `diyformdata` text,
  `diyformfields` text,
  `storeid` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=65 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_order_comment`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_order_comment` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `orderid` int(11) DEFAULT '0',
  `goodsid` int(11) DEFAULT '0',
  `openid` varchar(50) DEFAULT '',
  `nickname` varchar(50) DEFAULT '',
  `headimgurl` varchar(255) DEFAULT '',
  `level` tinyint(3) DEFAULT '0',
  `content` varchar(255) DEFAULT '',
  `images` text,
  `createtime` int(11) DEFAULT '0',
  `deleted` tinyint(3) DEFAULT '0',
  `append_content` varchar(255) DEFAULT '',
  `append_images` text,
  `reply_content` varchar(255) DEFAULT '',
  `reply_images` text,
  `append_reply_content` varchar(255) DEFAULT '',
  `append_reply_images` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_order_goods`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_order_goods` (
  `id` int(11) NOT NULL,
  `openid` varchar(255) DEFAULT NULL,
  `uniacid` int(11) DEFAULT '0',
  `orderid` int(11) DEFAULT '0',
  `goodsid` int(11) DEFAULT '0',
  `price` decimal(10,2) DEFAULT '0.00',
  `total` int(11) DEFAULT '1',
  `optionid` int(10) DEFAULT '0',
  `createtime` int(11) DEFAULT '0',
  `optionname` text,
  `commission1` text COMMENT '0',
  `applytime1` int(11) DEFAULT '0',
  `checktime1` int(10) DEFAULT '0',
  `paytime1` int(11) DEFAULT '0',
  `invalidtime1` int(11) DEFAULT '0',
  `deletetime1` int(11) DEFAULT '0',
  `status1` tinyint(3) DEFAULT '0' COMMENT '申请状态，-2删除，-1无效，0未申请，1申请，2审核通过 3已打款',
  `content1` text,
  `commission2` text,
  `applytime2` int(11) DEFAULT '0',
  `checktime2` int(10) DEFAULT '0',
  `paytime2` int(11) DEFAULT '0',
  `invalidtime2` int(11) DEFAULT '0',
  `deletetime2` int(11) DEFAULT '0',
  `status2` tinyint(3) DEFAULT '0' COMMENT '申请状态，-2删除，-1无效，0未申请，1申请，2审核通过 3已打款',
  `content2` text,
  `commission3` text,
  `applytime3` int(11) DEFAULT '0',
  `checktime3` int(10) DEFAULT '0',
  `paytime3` int(11) DEFAULT '0',
  `invalidtime3` int(11) DEFAULT '0',
  `deletetime3` int(11) DEFAULT '0',
  `status3` tinyint(3) DEFAULT '0' COMMENT '申请状态，-2删除，-1无效，0未申请，1申请，2审核通过 3已打款',
  `content3` text,
  `realprice` decimal(10,2) DEFAULT '0.00',
  `goodssn` varchar(255) DEFAULT '',
  `productsn` varchar(255) DEFAULT '',
  `nocommission` tinyint(3) DEFAULT '0',
  `changeprice` decimal(10,2) DEFAULT '0.00',
  `oldprice` decimal(10,2) DEFAULT '0.00',
  `commissions` text,
  `supplier_uid` int(11) NOT NULL COMMENT '供应商ID',
  `supplier_apply_status` tinyint(4) NOT NULL COMMENT '1为供应商已提现',
  `printstate` tinyint(3) DEFAULT '0',
  `printstate2` tinyint(3) DEFAULT '0',
  `diyformdataid` int(11) DEFAULT '0',
  `diyformid` int(11) DEFAULT '0',
  `diyformdata` text,
  `diyformfields` text
) ENGINE=MyISAM AUTO_INCREMENT=64 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_order_refund`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_order_refund` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `orderid` int(11) DEFAULT '0',
  `refundno` varchar(255) DEFAULT '',
  `price` varchar(255) DEFAULT '',
  `reason` varchar(255) DEFAULT '',
  `images` text,
  `content` text,
  `createtime` int(11) DEFAULT '0',
  `status` tinyint(3) DEFAULT '0' COMMENT '0申请 1 通过 2 驳回',
  `reply` text,
  `refundtype` tinyint(3) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_perm_log`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_perm_log` (
  `id` int(11) NOT NULL,
  `uid` int(11) DEFAULT '0',
  `uniacid` int(11) DEFAULT '0',
  `name` varchar(255) DEFAULT '',
  `type` varchar(255) DEFAULT '',
  `op` text,
  `createtime` int(11) DEFAULT '0',
  `ip` varchar(255) DEFAULT ''
) ENGINE=MyISAM AUTO_INCREMENT=522 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_perm_plugin`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_perm_plugin` (
  `id` int(11) NOT NULL,
  `acid` int(11) DEFAULT '0',
  `uid` int(11) DEFAULT '0',
  `type` tinyint(3) DEFAULT '0',
  `plugins` text
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_perm_role`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_perm_role` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `rolename` varchar(255) DEFAULT '',
  `status` tinyint(3) DEFAULT '0',
  `perms` text,
  `deleted` tinyint(3) DEFAULT '0',
  `status1` tinyint(3) NOT NULL COMMENT '1：供应商开启'
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_perm_user`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_perm_user` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `uid` int(11) DEFAULT '0',
  `username` varchar(255) DEFAULT '',
  `password` varchar(255) DEFAULT '',
  `roleid` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '0',
  `perms` text,
  `deleted` tinyint(3) DEFAULT '0',
  `realname` varchar(255) DEFAULT '',
  `mobile` varchar(255) DEFAULT '',
  `banknumber` varchar(255) NOT NULL COMMENT '银行卡号',
  `accountname` varchar(255) NOT NULL COMMENT '开户名',
  `accountbank` varchar(255) NOT NULL COMMENT '开户行',
  `openid` varchar(255) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_plugin`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_plugin` (
  `id` int(11) NOT NULL,
  `displayorder` int(11) DEFAULT '0',
  `identity` varchar(50) DEFAULT '',
  `name` varchar(50) DEFAULT '',
  `version` varchar(10) DEFAULT '',
  `author` varchar(20) DEFAULT '',
  `status` tinyint(3) DEFAULT '0',
  `category` varchar(255) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=1003 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_poster`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_poster` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `type` tinyint(3) DEFAULT '0' COMMENT '1 首页 2 小店 3 商城 4 自定义',
  `title` varchar(255) DEFAULT '',
  `bg` varchar(255) DEFAULT '',
  `data` text,
  `keyword` varchar(255) DEFAULT '',
  `times` int(11) DEFAULT '0',
  `follows` int(11) DEFAULT '0',
  `isdefault` tinyint(3) DEFAULT '0',
  `resptitle` varchar(255) DEFAULT '',
  `respthumb` varchar(255) DEFAULT '',
  `createtime` int(11) DEFAULT '0',
  `respdesc` varchar(255) DEFAULT '',
  `respurl` varchar(255) DEFAULT '',
  `waittext` varchar(255) DEFAULT '',
  `oktext` varchar(255) DEFAULT '',
  `subcredit` int(11) DEFAULT '0',
  `submoney` decimal(10,2) DEFAULT '0.00',
  `reccredit` int(11) DEFAULT '0',
  `recmoney` decimal(10,2) DEFAULT '0.00',
  `paytype` tinyint(1) DEFAULT '0',
  `scantext` varchar(255) DEFAULT '',
  `subtext` varchar(255) DEFAULT '',
  `beagent` tinyint(3) DEFAULT '0',
  `bedown` tinyint(3) DEFAULT '0',
  `isopen` tinyint(3) DEFAULT '0',
  `opentext` varchar(255) DEFAULT '',
  `openurl` varchar(255) DEFAULT '',
  `templateid` varchar(255) DEFAULT '',
  `subpaycontent` text,
  `recpaycontent` text,
  `entrytext` varchar(255) DEFAULT '',
  `reccouponid` int(11) DEFAULT '0',
  `reccouponnum` int(11) DEFAULT '0',
  `subcouponid` int(11) DEFAULT '0',
  `subcouponnum` int(11) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_postera`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_postera` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `type` tinyint(3) DEFAULT '0' COMMENT '1 首页 2 小店 3 商城 4 自定义',
  `days` int(11) DEFAULT '0',
  `title` varchar(255) DEFAULT '',
  `bg` varchar(255) DEFAULT '',
  `data` text,
  `keyword` varchar(255) DEFAULT '',
  `isdefault` tinyint(3) DEFAULT '0',
  `resptitle` varchar(255) DEFAULT '',
  `respthumb` varchar(255) DEFAULT '',
  `createtime` int(11) DEFAULT '0',
  `respdesc` varchar(255) DEFAULT '',
  `respurl` varchar(255) DEFAULT '',
  `waittext` varchar(255) DEFAULT '',
  `oktext` varchar(255) DEFAULT '',
  `subcredit` int(11) DEFAULT '0',
  `submoney` decimal(10,2) DEFAULT '0.00',
  `reccredit` int(11) DEFAULT '0',
  `recmoney` decimal(10,2) DEFAULT '0.00',
  `scantext` varchar(255) DEFAULT '',
  `subtext` varchar(255) DEFAULT '',
  `beagent` tinyint(3) DEFAULT '0',
  `bedown` tinyint(3) DEFAULT '0',
  `isopen` tinyint(3) DEFAULT '0',
  `opentext` varchar(255) DEFAULT '',
  `openurl` varchar(255) DEFAULT '',
  `paytype` tinyint(1) NOT NULL DEFAULT '0',
  `subpaycontent` text,
  `recpaycontent` varchar(255) DEFAULT '',
  `templateid` varchar(255) DEFAULT '',
  `entrytext` varchar(255) DEFAULT '',
  `reccouponid` int(11) DEFAULT '0',
  `reccouponnum` int(11) DEFAULT '0',
  `subcouponid` int(11) DEFAULT '0',
  `subcouponnum` int(11) DEFAULT '0',
  `timestart` int(11) DEFAULT '0',
  `timeend` int(11) DEFAULT '0',
  `status` tinyint(3) DEFAULT '0',
  `goodsid` int(11) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_postera_log`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_postera_log` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT '',
  `posterid` int(11) DEFAULT '0',
  `from_openid` varchar(255) DEFAULT '',
  `subcredit` int(11) DEFAULT '0',
  `submoney` decimal(10,2) DEFAULT '0.00',
  `reccredit` int(11) DEFAULT '0',
  `recmoney` decimal(10,2) DEFAULT '0.00',
  `createtime` int(11) DEFAULT '0',
  `reccouponid` int(11) DEFAULT '0',
  `reccouponnum` int(11) DEFAULT '0',
  `subcouponid` int(11) DEFAULT '0',
  `subcouponnum` int(11) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_postera_qr`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_postera_qr` (
  `id` int(11) NOT NULL,
  `acid` int(10) unsigned NOT NULL,
  `openid` varchar(100) NOT NULL DEFAULT '',
  `posterid` int(11) DEFAULT '0',
  `type` tinyint(3) DEFAULT '0',
  `sceneid` int(11) DEFAULT '0',
  `mediaid` varchar(255) DEFAULT '',
  `ticket` varchar(250) NOT NULL,
  `url` varchar(80) NOT NULL,
  `createtime` int(10) unsigned NOT NULL,
  `goodsid` int(11) DEFAULT '0',
  `qrimg` varchar(1000) DEFAULT '',
  `expire` int(11) DEFAULT '0',
  `endtime` int(11) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_poster_log`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_poster_log` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT '',
  `posterid` int(11) DEFAULT '0',
  `from_openid` varchar(255) DEFAULT '',
  `subcredit` int(11) DEFAULT '0',
  `submoney` decimal(10,2) DEFAULT '0.00',
  `reccredit` int(11) DEFAULT '0',
  `recmoney` decimal(10,2) DEFAULT '0.00',
  `createtime` int(11) DEFAULT '0',
  `reccouponid` int(11) DEFAULT '0',
  `reccouponnum` int(11) DEFAULT '0',
  `subcouponid` int(11) DEFAULT '0',
  `subcouponnum` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_poster_qr`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_poster_qr` (
  `id` int(11) NOT NULL,
  `acid` int(10) unsigned NOT NULL,
  `openid` varchar(100) NOT NULL DEFAULT '',
  `type` tinyint(3) DEFAULT '0',
  `sceneid` int(11) DEFAULT '0',
  `mediaid` varchar(255) DEFAULT '',
  `ticket` varchar(250) NOT NULL,
  `url` varchar(80) NOT NULL,
  `createtime` int(10) unsigned NOT NULL,
  `goodsid` int(11) DEFAULT '0',
  `qrimg` varchar(1000) DEFAULT '',
  `scenestr` varchar(255) DEFAULT ''
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_poster_scan`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_poster_scan` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `posterid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT '',
  `from_openid` varchar(255) DEFAULT '',
  `scantime` int(11) DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_saler`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_saler` (
  `id` int(11) NOT NULL,
  `storeid` int(11) DEFAULT '0',
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(255) DEFAULT '',
  `status` tinyint(3) DEFAULT '0',
  `salername` varchar(255) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_store`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_store` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `storename` varchar(255) DEFAULT '',
  `address` varchar(255) DEFAULT '',
  `tel` varchar(255) DEFAULT '',
  `lat` varchar(255) DEFAULT '',
  `lng` varchar(255) DEFAULT '',
  `status` tinyint(3) DEFAULT '0',
  `realname` varchar(255) DEFAULT '',
  `mobile` varchar(255) DEFAULT '',
  `fetchtime` varchar(255) DEFAULT '',
  `type` tinyint(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_supplier_apply`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_supplier_apply` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL COMMENT '供应商id',
  `type` int(11) NOT NULL COMMENT '1手动2微信',
  `applysn` varchar(255) NOT NULL COMMENT '提现单号',
  `apply_money` int(11) NOT NULL COMMENT '申请金额',
  `apply_time` int(11) NOT NULL COMMENT '申请时间',
  `status` tinyint(3) NOT NULL COMMENT '0为申请状态1为完成状态',
  `finish_time` int(11) NOT NULL COMMENT '完成时间'
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_sysset`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_sysset` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0',
  `sets` text,
  `plugins` text,
  `sec` text
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_virtual_category`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_virtual_category` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT '0' COMMENT '所属帐号',
  `name` varchar(50) DEFAULT NULL COMMENT '分类名称'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_virtual_data`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_virtual_data` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `typeid` int(11) NOT NULL DEFAULT '0' COMMENT '类型id',
  `pvalue` varchar(255) DEFAULT '' COMMENT '主键键值',
  `fields` text NOT NULL COMMENT '字符集',
  `openid` varchar(255) NOT NULL DEFAULT '' COMMENT '使用者openid',
  `usetime` int(11) NOT NULL DEFAULT '0' COMMENT '使用时间',
  `orderid` int(11) DEFAULT '0',
  `ordersn` varchar(255) DEFAULT '',
  `price` decimal(10,2) DEFAULT '0.00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_sz_yi_virtual_type`
--

CREATE TABLE IF NOT EXISTS `ims_sz_yi_virtual_type` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `cate` int(11) DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '分类名称',
  `fields` text NOT NULL COMMENT '字段集',
  `usedata` int(11) NOT NULL DEFAULT '0' COMMENT '已用数据',
  `alldata` int(11) NOT NULL DEFAULT '0' COMMENT '全部数据'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ims_sz_yi_adv`
--
ALTER TABLE `ims_sz_yi_adv`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_enabled` (`enabled`), ADD KEY `idx_displayorder` (`displayorder`);

--
-- Indexes for table `ims_sz_yi_af_supplier`
--
ALTER TABLE `ims_sz_yi_af_supplier`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ims_sz_yi_article`
--
ALTER TABLE `ims_sz_yi_article`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_article_title` (`article_title`), ADD KEY `idx_article_content` (`article_content`(10)), ADD KEY `idx_article_keyword` (`article_keyword`), ADD KEY `idx_uniacid` (`uniacid`);

--
-- Indexes for table `ims_sz_yi_article_category`
--
ALTER TABLE `ims_sz_yi_article_category`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_category_name` (`category_name`);

--
-- Indexes for table `ims_sz_yi_article_log`
--
ALTER TABLE `ims_sz_yi_article_log`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_aid` (`aid`), ADD KEY `idx_openid` (`openid`), ADD KEY `idx_uniacid` (`uniacid`);

--
-- Indexes for table `ims_sz_yi_article_report`
--
ALTER TABLE `ims_sz_yi_article_report`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ims_sz_yi_article_share`
--
ALTER TABLE `ims_sz_yi_article_share`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_aid` (`aid`), ADD KEY `idx_uniacid` (`uniacid`);

--
-- Indexes for table `ims_sz_yi_article_sys`
--
ALTER TABLE `ims_sz_yi_article_sys`
  ADD PRIMARY KEY (`uniacid`), ADD KEY `idx_article_message` (`article_message`), ADD KEY `idx_article_keyword` (`article_keyword`), ADD KEY `idx_article_title` (`article_title`);

--
-- Indexes for table `ims_sz_yi_bonus`
--
ALTER TABLE `ims_sz_yi_bonus`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`);

--
-- Indexes for table `ims_sz_yi_bonus_goods`
--
ALTER TABLE `ims_sz_yi_bonus_goods`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`);

--
-- Indexes for table `ims_sz_yi_bonus_level`
--
ALTER TABLE `ims_sz_yi_bonus_level`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`);

--
-- Indexes for table `ims_sz_yi_bonus_log`
--
ALTER TABLE `ims_sz_yi_bonus_log`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`);

--
-- Indexes for table `ims_sz_yi_carrier`
--
ALTER TABLE `ims_sz_yi_carrier`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_deleted` (`deleted`), ADD KEY `idx_createtime` (`createtime`);

--
-- Indexes for table `ims_sz_yi_category`
--
ALTER TABLE `ims_sz_yi_category`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_displayorder` (`displayorder`), ADD KEY `idx_enabled` (`enabled`), ADD KEY `idx_parentid` (`parentid`), ADD KEY `idx_isrecommand` (`isrecommand`), ADD KEY `idx_ishome` (`ishome`);

--
-- Indexes for table `ims_sz_yi_commission_apply`
--
ALTER TABLE `ims_sz_yi_commission_apply`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_mid` (`mid`), ADD KEY `idx_checktime` (`checktime`), ADD KEY `idx_paytime` (`paytime`), ADD KEY `idx_applytime` (`applytime`), ADD KEY `idx_status` (`status`), ADD KEY `idx_invalidtime` (`invalidtime`);

--
-- Indexes for table `ims_sz_yi_commission_clickcount`
--
ALTER TABLE `ims_sz_yi_commission_clickcount`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_openid` (`openid`), ADD KEY `idx_from_openid` (`from_openid`);

--
-- Indexes for table `ims_sz_yi_commission_level`
--
ALTER TABLE `ims_sz_yi_commission_level`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`);

--
-- Indexes for table `ims_sz_yi_commission_log`
--
ALTER TABLE `ims_sz_yi_commission_log`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_applyid` (`applyid`), ADD KEY `idx_mid` (`mid`), ADD KEY `idx_createtime` (`createtime`);

--
-- Indexes for table `ims_sz_yi_commission_shop`
--
ALTER TABLE `ims_sz_yi_commission_shop`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_mid` (`mid`);

--
-- Indexes for table `ims_sz_yi_coupon`
--
ALTER TABLE `ims_sz_yi_coupon`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_coupontype` (`coupontype`), ADD KEY `idx_timestart` (`timestart`), ADD KEY `idx_timeend` (`timeend`), ADD KEY `idx_timelimit` (`timelimit`), ADD KEY `idx_status` (`status`), ADD KEY `idx_givetype` (`backtype`), ADD KEY `idx_catid` (`catid`);

--
-- Indexes for table `ims_sz_yi_coupon_category`
--
ALTER TABLE `ims_sz_yi_coupon_category`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_displayorder` (`displayorder`), ADD KEY `idx_status` (`status`);

--
-- Indexes for table `ims_sz_yi_coupon_data`
--
ALTER TABLE `ims_sz_yi_coupon_data`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_couponid` (`couponid`), ADD KEY `idx_gettype` (`gettype`);

--
-- Indexes for table `ims_sz_yi_coupon_guess`
--
ALTER TABLE `ims_sz_yi_coupon_guess`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_couponid` (`couponid`);

--
-- Indexes for table `ims_sz_yi_coupon_log`
--
ALTER TABLE `ims_sz_yi_coupon_log`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_couponid` (`couponid`), ADD KEY `idx_status` (`status`), ADD KEY `idx_paystatus` (`paystatus`), ADD KEY `idx_createtime` (`createtime`), ADD KEY `idx_getfrom` (`getfrom`);

--
-- Indexes for table `ims_sz_yi_creditshop_adv`
--
ALTER TABLE `ims_sz_yi_creditshop_adv`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_enabled` (`enabled`), ADD KEY `idx_displayorder` (`displayorder`);

--
-- Indexes for table `ims_sz_yi_creditshop_category`
--
ALTER TABLE `ims_sz_yi_creditshop_category`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_displayorder` (`displayorder`), ADD KEY `idx_enabled` (`enabled`);

--
-- Indexes for table `ims_sz_yi_creditshop_goods`
--
ALTER TABLE `ims_sz_yi_creditshop_goods`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_type` (`type`), ADD KEY `idx_endtime` (`endtime`), ADD KEY `idx_createtime` (`createtime`), ADD KEY `idx_status` (`status`), ADD KEY `idx_displayorder` (`displayorder`), ADD KEY `idx_deleted` (`deleted`), ADD KEY `idx_istop` (`istop`), ADD KEY `idx_isrecommand` (`isrecommand`), ADD KEY `idx_istime` (`istime`), ADD KEY `idx_timestart` (`timestart`), ADD KEY `idx_timeend` (`timeend`), ADD KEY `idx_goodstype` (`goodstype`);

--
-- Indexes for table `ims_sz_yi_creditshop_log`
--
ALTER TABLE `ims_sz_yi_creditshop_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ims_sz_yi_designer`
--
ALTER TABLE `ims_sz_yi_designer`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_pagetype` (`pagetype`), ADD FULLTEXT KEY `idx_keyword` (`keyword`);

--
-- Indexes for table `ims_sz_yi_designer_menu`
--
ALTER TABLE `ims_sz_yi_designer_menu`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_isdefault` (`isdefault`), ADD KEY `idx_createtime` (`createtime`);

--
-- Indexes for table `ims_sz_yi_dispatch`
--
ALTER TABLE `ims_sz_yi_dispatch`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_displayorder` (`displayorder`);

--
-- Indexes for table `ims_sz_yi_diyform_category`
--
ALTER TABLE `ims_sz_yi_diyform_category`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`) USING BTREE;

--
-- Indexes for table `ims_sz_yi_diyform_data`
--
ALTER TABLE `ims_sz_yi_diyform_data`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`) USING BTREE, ADD KEY `idx_typeid` (`typeid`) USING BTREE, ADD KEY `idx_cid` (`cid`) USING BTREE;

--
-- Indexes for table `ims_sz_yi_diyform_temp`
--
ALTER TABLE `ims_sz_yi_diyform_temp`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`) USING BTREE, ADD KEY `idx_cid` (`cid`) USING BTREE;

--
-- Indexes for table `ims_sz_yi_diyform_type`
--
ALTER TABLE `ims_sz_yi_diyform_type`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`) USING BTREE, ADD KEY `idx_cate` (`cate`) USING BTREE;

--
-- Indexes for table `ims_sz_yi_exhelper_express`
--
ALTER TABLE `ims_sz_yi_exhelper_express`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_isdefault` (`isdefault`);

--
-- Indexes for table `ims_sz_yi_exhelper_senduser`
--
ALTER TABLE `ims_sz_yi_exhelper_senduser`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_isdefault` (`isdefault`);

--
-- Indexes for table `ims_sz_yi_exhelper_sys`
--
ALTER TABLE `ims_sz_yi_exhelper_sys`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ims_sz_yi_express`
--
ALTER TABLE `ims_sz_yi_express`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_displayorder` (`displayorder`);

--
-- Indexes for table `ims_sz_yi_feedback`
--
ALTER TABLE `ims_sz_yi_feedback`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_feedbackid` (`feedbackid`), ADD KEY `idx_createtime` (`createtime`), ADD KEY `idx_transid` (`transid`);

--
-- Indexes for table `ims_sz_yi_goods`
--
ALTER TABLE `ims_sz_yi_goods`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_pcate` (`pcate`), ADD KEY `idx_ccate` (`ccate`), ADD KEY `idx_isnew` (`isnew`), ADD KEY `idx_ishot` (`ishot`), ADD KEY `idx_isdiscount` (`isdiscount`), ADD KEY `idx_isrecommand` (`isrecommand`), ADD KEY `idx_iscomment` (`iscomment`), ADD KEY `idx_issendfree` (`issendfree`), ADD KEY `idx_istime` (`istime`), ADD KEY `idx_deleted` (`deleted`), ADD KEY `idx_tcate` (`tcate`), ADD FULLTEXT KEY `idx_buylevels` (`buylevels`), ADD FULLTEXT KEY `idx_showgroups` (`showgroups`), ADD FULLTEXT KEY `idx_buygroups` (`buygroups`);

--
-- Indexes for table `ims_sz_yi_goods_comment`
--
ALTER TABLE `ims_sz_yi_goods_comment`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_goodsid` (`goodsid`), ADD KEY `idx_openid` (`openid`), ADD KEY `idx_createtime` (`createtime`);

--
-- Indexes for table `ims_sz_yi_goods_option`
--
ALTER TABLE `ims_sz_yi_goods_option`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_goodsid` (`goodsid`), ADD KEY `idx_displayorder` (`displayorder`);

--
-- Indexes for table `ims_sz_yi_goods_param`
--
ALTER TABLE `ims_sz_yi_goods_param`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_goodsid` (`goodsid`), ADD KEY `idx_displayorder` (`displayorder`);

--
-- Indexes for table `ims_sz_yi_goods_spec`
--
ALTER TABLE `ims_sz_yi_goods_spec`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_goodsid` (`goodsid`), ADD KEY `idx_displayorder` (`displayorder`);

--
-- Indexes for table `ims_sz_yi_goods_spec_item`
--
ALTER TABLE `ims_sz_yi_goods_spec_item`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_specid` (`specid`), ADD KEY `idx_show` (`show`), ADD KEY `idx_displayorder` (`displayorder`);

--
-- Indexes for table `ims_sz_yi_member`
--
ALTER TABLE `ims_sz_yi_member`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_shareid` (`agentid`), ADD KEY `idx_openid` (`openid`), ADD KEY `idx_status` (`status`), ADD KEY `idx_agenttime` (`agenttime`), ADD KEY `idx_isagent` (`isagent`), ADD KEY `idx_uid` (`uid`), ADD KEY `idx_groupid` (`groupid`), ADD KEY `idx_level` (`level`);

--
-- Indexes for table `ims_sz_yi_member_address`
--
ALTER TABLE `ims_sz_yi_member_address`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_openid` (`openid`), ADD KEY `idx_isdefault` (`isdefault`), ADD KEY `idx_deleted` (`deleted`);

--
-- Indexes for table `ims_sz_yi_member_cart`
--
ALTER TABLE `ims_sz_yi_member_cart`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_goodsid` (`goodsid`), ADD KEY `idx_openid` (`openid`), ADD KEY `idx_deleted` (`deleted`);

--
-- Indexes for table `ims_sz_yi_member_favorite`
--
ALTER TABLE `ims_sz_yi_member_favorite`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_goodsid` (`goodsid`), ADD KEY `idx_openid` (`openid`), ADD KEY `idx_deleted` (`deleted`), ADD KEY `idx_createtime` (`createtime`);

--
-- Indexes for table `ims_sz_yi_member_group`
--
ALTER TABLE `ims_sz_yi_member_group`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ims_sz_yi_member_history`
--
ALTER TABLE `ims_sz_yi_member_history`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_goodsid` (`goodsid`), ADD KEY `idx_openid` (`openid`), ADD KEY `idx_deleted` (`deleted`), ADD KEY `idx_createtime` (`createtime`);

--
-- Indexes for table `ims_sz_yi_member_level`
--
ALTER TABLE `ims_sz_yi_member_level`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`);

--
-- Indexes for table `ims_sz_yi_member_log`
--
ALTER TABLE `ims_sz_yi_member_log`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_openid` (`openid`), ADD KEY `idx_type` (`type`), ADD KEY `idx_createtime` (`createtime`), ADD KEY `idx_status` (`status`);

--
-- Indexes for table `ims_sz_yi_member_message_template`
--
ALTER TABLE `ims_sz_yi_member_message_template`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_createtime` (`createtime`);

--
-- Indexes for table `ims_sz_yi_notice`
--
ALTER TABLE `ims_sz_yi_notice`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`);

--
-- Indexes for table `ims_sz_yi_order`
--
ALTER TABLE `ims_sz_yi_order`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_openid` (`openid`), ADD KEY `idx_shareid` (`agentid`), ADD KEY `idx_status` (`status`), ADD KEY `idx_createtime` (`createtime`), ADD KEY `idx_refundid` (`refundid`), ADD KEY `idx_paytime` (`paytime`), ADD KEY `idx_finishtime` (`finishtime`);

--
-- Indexes for table `ims_sz_yi_order_comment`
--
ALTER TABLE `ims_sz_yi_order_comment`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_goodsid` (`goodsid`), ADD KEY `idx_openid` (`openid`), ADD KEY `idx_createtime` (`createtime`), ADD KEY `idx_orderid` (`orderid`);

--
-- Indexes for table `ims_sz_yi_order_goods`
--
ALTER TABLE `ims_sz_yi_order_goods`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_orderid` (`orderid`), ADD KEY `idx_goodsid` (`goodsid`), ADD KEY `idx_createtime` (`createtime`), ADD KEY `idx_applytime1` (`applytime1`), ADD KEY `idx_checktime1` (`checktime1`), ADD KEY `idx_status1` (`status1`), ADD KEY `idx_applytime2` (`applytime2`), ADD KEY `idx_checktime2` (`checktime2`), ADD KEY `idx_status2` (`status2`), ADD KEY `idx_applytime3` (`applytime3`), ADD KEY `idx_invalidtime1` (`invalidtime1`), ADD KEY `idx_checktime3` (`checktime3`), ADD KEY `idx_invalidtime2` (`invalidtime2`), ADD KEY `idx_invalidtime3` (`invalidtime3`), ADD KEY `idx_status3` (`status3`), ADD KEY `idx_paytime1` (`paytime1`), ADD KEY `idx_paytime2` (`paytime2`), ADD KEY `idx_paytime3` (`paytime3`);

--
-- Indexes for table `ims_sz_yi_order_refund`
--
ALTER TABLE `ims_sz_yi_order_refund`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_createtime` (`createtime`), ADD KEY `idx_uniacid` (`uniacid`);

--
-- Indexes for table `ims_sz_yi_perm_log`
--
ALTER TABLE `ims_sz_yi_perm_log`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uid` (`uid`), ADD KEY `idx_createtime` (`createtime`), ADD KEY `idx_uniacid` (`uniacid`), ADD FULLTEXT KEY `idx_type` (`type`), ADD FULLTEXT KEY `idx_op` (`op`);

--
-- Indexes for table `ims_sz_yi_perm_plugin`
--
ALTER TABLE `ims_sz_yi_perm_plugin`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uid` (`uid`), ADD KEY `idx_acid` (`acid`), ADD KEY `idx_type` (`type`);

--
-- Indexes for table `ims_sz_yi_perm_role`
--
ALTER TABLE `ims_sz_yi_perm_role`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_status` (`status`), ADD KEY `idx_deleted` (`deleted`);

--
-- Indexes for table `ims_sz_yi_perm_user`
--
ALTER TABLE `ims_sz_yi_perm_user`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_uid` (`uid`), ADD KEY `idx_roleid` (`roleid`), ADD KEY `idx_status` (`status`), ADD KEY `idx_deleted` (`deleted`);

--
-- Indexes for table `ims_sz_yi_plugin`
--
ALTER TABLE `ims_sz_yi_plugin`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_displayorder` (`displayorder`), ADD FULLTEXT KEY `idx_identity` (`identity`);

--
-- Indexes for table `ims_sz_yi_poster`
--
ALTER TABLE `ims_sz_yi_poster`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_type` (`type`), ADD KEY `idx_times` (`times`), ADD KEY `idx_isdefault` (`isdefault`), ADD KEY `idx_createtime` (`createtime`);

--
-- Indexes for table `ims_sz_yi_postera`
--
ALTER TABLE `ims_sz_yi_postera`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_type` (`type`), ADD KEY `idx_isdefault` (`isdefault`), ADD KEY `idx_createtime` (`createtime`);

--
-- Indexes for table `ims_sz_yi_postera_log`
--
ALTER TABLE `ims_sz_yi_postera_log`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_openid` (`openid`), ADD KEY `idx_createtime` (`createtime`), ADD KEY `idx_posteraid` (`posterid`), ADD FULLTEXT KEY `idx_from_openid` (`from_openid`);

--
-- Indexes for table `ims_sz_yi_postera_qr`
--
ALTER TABLE `ims_sz_yi_postera_qr`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_acid` (`acid`), ADD KEY `idx_sceneid` (`sceneid`), ADD KEY `idx_type` (`type`), ADD KEY `idx_posterid` (`posterid`);

--
-- Indexes for table `ims_sz_yi_poster_log`
--
ALTER TABLE `ims_sz_yi_poster_log`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_openid` (`openid`), ADD KEY `idx_createtime` (`createtime`), ADD KEY `idx_posterid` (`posterid`), ADD FULLTEXT KEY `idx_from_openid` (`from_openid`);

--
-- Indexes for table `ims_sz_yi_poster_qr`
--
ALTER TABLE `ims_sz_yi_poster_qr`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_acid` (`acid`), ADD KEY `idx_sceneid` (`sceneid`), ADD KEY `idx_type` (`type`), ADD FULLTEXT KEY `idx_openid` (`openid`);

--
-- Indexes for table `ims_sz_yi_poster_scan`
--
ALTER TABLE `ims_sz_yi_poster_scan`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_posterid` (`posterid`), ADD KEY `idx_scantime` (`scantime`), ADD FULLTEXT KEY `idx_openid` (`openid`);

--
-- Indexes for table `ims_sz_yi_saler`
--
ALTER TABLE `ims_sz_yi_saler`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_storeid` (`storeid`), ADD KEY `idx_uniacid` (`uniacid`);

--
-- Indexes for table `ims_sz_yi_store`
--
ALTER TABLE `ims_sz_yi_store`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_status` (`status`);

--
-- Indexes for table `ims_sz_yi_supplier_apply`
--
ALTER TABLE `ims_sz_yi_supplier_apply`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ims_sz_yi_sysset`
--
ALTER TABLE `ims_sz_yi_sysset`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`);

--
-- Indexes for table `ims_sz_yi_virtual_category`
--
ALTER TABLE `ims_sz_yi_virtual_category`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`);

--
-- Indexes for table `ims_sz_yi_virtual_data`
--
ALTER TABLE `ims_sz_yi_virtual_data`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_typeid` (`typeid`), ADD KEY `idx_usetime` (`usetime`), ADD KEY `idx_orderid` (`orderid`);

--
-- Indexes for table `ims_sz_yi_virtual_type`
--
ALTER TABLE `ims_sz_yi_virtual_type`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_uniacid` (`uniacid`), ADD KEY `idx_cate` (`cate`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ims_sz_yi_adv`
--
ALTER TABLE `ims_sz_yi_adv`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_af_supplier`
--
ALTER TABLE `ims_sz_yi_af_supplier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_article`
--
ALTER TABLE `ims_sz_yi_article`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_article_category`
--
ALTER TABLE `ims_sz_yi_article_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_article_log`
--
ALTER TABLE `ims_sz_yi_article_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_article_report`
--
ALTER TABLE `ims_sz_yi_article_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_article_share`
--
ALTER TABLE `ims_sz_yi_article_share`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_bonus`
--
ALTER TABLE `ims_sz_yi_bonus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_bonus_goods`
--
ALTER TABLE `ims_sz_yi_bonus_goods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_bonus_level`
--
ALTER TABLE `ims_sz_yi_bonus_level`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_bonus_log`
--
ALTER TABLE `ims_sz_yi_bonus_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_carrier`
--
ALTER TABLE `ims_sz_yi_carrier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_category`
--
ALTER TABLE `ims_sz_yi_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `ims_sz_yi_commission_apply`
--
ALTER TABLE `ims_sz_yi_commission_apply`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_commission_clickcount`
--
ALTER TABLE `ims_sz_yi_commission_clickcount`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `ims_sz_yi_commission_level`
--
ALTER TABLE `ims_sz_yi_commission_level`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_commission_log`
--
ALTER TABLE `ims_sz_yi_commission_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_commission_shop`
--
ALTER TABLE `ims_sz_yi_commission_shop`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_coupon`
--
ALTER TABLE `ims_sz_yi_coupon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `ims_sz_yi_coupon_category`
--
ALTER TABLE `ims_sz_yi_coupon_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `ims_sz_yi_coupon_data`
--
ALTER TABLE `ims_sz_yi_coupon_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `ims_sz_yi_coupon_guess`
--
ALTER TABLE `ims_sz_yi_coupon_guess`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `ims_sz_yi_coupon_log`
--
ALTER TABLE `ims_sz_yi_coupon_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `ims_sz_yi_creditshop_adv`
--
ALTER TABLE `ims_sz_yi_creditshop_adv`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_creditshop_category`
--
ALTER TABLE `ims_sz_yi_creditshop_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_creditshop_goods`
--
ALTER TABLE `ims_sz_yi_creditshop_goods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_creditshop_log`
--
ALTER TABLE `ims_sz_yi_creditshop_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_designer`
--
ALTER TABLE `ims_sz_yi_designer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `ims_sz_yi_designer_menu`
--
ALTER TABLE `ims_sz_yi_designer_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `ims_sz_yi_dispatch`
--
ALTER TABLE `ims_sz_yi_dispatch`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `ims_sz_yi_diyform_category`
--
ALTER TABLE `ims_sz_yi_diyform_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `ims_sz_yi_diyform_data`
--
ALTER TABLE `ims_sz_yi_diyform_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_diyform_temp`
--
ALTER TABLE `ims_sz_yi_diyform_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `ims_sz_yi_diyform_type`
--
ALTER TABLE `ims_sz_yi_diyform_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `ims_sz_yi_exhelper_express`
--
ALTER TABLE `ims_sz_yi_exhelper_express`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `ims_sz_yi_exhelper_senduser`
--
ALTER TABLE `ims_sz_yi_exhelper_senduser`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_exhelper_sys`
--
ALTER TABLE `ims_sz_yi_exhelper_sys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_express`
--
ALTER TABLE `ims_sz_yi_express`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_feedback`
--
ALTER TABLE `ims_sz_yi_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_goods`
--
ALTER TABLE `ims_sz_yi_goods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `ims_sz_yi_goods_comment`
--
ALTER TABLE `ims_sz_yi_goods_comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_goods_option`
--
ALTER TABLE `ims_sz_yi_goods_option`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `ims_sz_yi_goods_param`
--
ALTER TABLE `ims_sz_yi_goods_param`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=34;
--
-- AUTO_INCREMENT for table `ims_sz_yi_goods_spec`
--
ALTER TABLE `ims_sz_yi_goods_spec`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `ims_sz_yi_goods_spec_item`
--
ALTER TABLE `ims_sz_yi_goods_spec_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `ims_sz_yi_member`
--
ALTER TABLE `ims_sz_yi_member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=52;
--
-- AUTO_INCREMENT for table `ims_sz_yi_member_address`
--
ALTER TABLE `ims_sz_yi_member_address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT for table `ims_sz_yi_member_cart`
--
ALTER TABLE `ims_sz_yi_member_cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `ims_sz_yi_member_favorite`
--
ALTER TABLE `ims_sz_yi_member_favorite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `ims_sz_yi_member_group`
--
ALTER TABLE `ims_sz_yi_member_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `ims_sz_yi_member_history`
--
ALTER TABLE `ims_sz_yi_member_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=244;
--
-- AUTO_INCREMENT for table `ims_sz_yi_member_level`
--
ALTER TABLE `ims_sz_yi_member_level`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_member_log`
--
ALTER TABLE `ims_sz_yi_member_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=26;
--
-- AUTO_INCREMENT for table `ims_sz_yi_member_message_template`
--
ALTER TABLE `ims_sz_yi_member_message_template`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_notice`
--
ALTER TABLE `ims_sz_yi_notice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_order`
--
ALTER TABLE `ims_sz_yi_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=65;
--
-- AUTO_INCREMENT for table `ims_sz_yi_order_comment`
--
ALTER TABLE `ims_sz_yi_order_comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_order_goods`
--
ALTER TABLE `ims_sz_yi_order_goods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=64;
--
-- AUTO_INCREMENT for table `ims_sz_yi_order_refund`
--
ALTER TABLE `ims_sz_yi_order_refund`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `ims_sz_yi_perm_log`
--
ALTER TABLE `ims_sz_yi_perm_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=522;
--
-- AUTO_INCREMENT for table `ims_sz_yi_perm_plugin`
--
ALTER TABLE `ims_sz_yi_perm_plugin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `ims_sz_yi_perm_role`
--
ALTER TABLE `ims_sz_yi_perm_role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `ims_sz_yi_perm_user`
--
ALTER TABLE `ims_sz_yi_perm_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `ims_sz_yi_plugin`
--
ALTER TABLE `ims_sz_yi_plugin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1003;
--
-- AUTO_INCREMENT for table `ims_sz_yi_poster`
--
ALTER TABLE `ims_sz_yi_poster`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `ims_sz_yi_postera`
--
ALTER TABLE `ims_sz_yi_postera`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `ims_sz_yi_postera_log`
--
ALTER TABLE `ims_sz_yi_postera_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `ims_sz_yi_postera_qr`
--
ALTER TABLE `ims_sz_yi_postera_qr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `ims_sz_yi_poster_log`
--
ALTER TABLE `ims_sz_yi_poster_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_poster_qr`
--
ALTER TABLE `ims_sz_yi_poster_qr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `ims_sz_yi_poster_scan`
--
ALTER TABLE `ims_sz_yi_poster_scan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `ims_sz_yi_saler`
--
ALTER TABLE `ims_sz_yi_saler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_store`
--
ALTER TABLE `ims_sz_yi_store`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_supplier_apply`
--
ALTER TABLE `ims_sz_yi_supplier_apply`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `ims_sz_yi_sysset`
--
ALTER TABLE `ims_sz_yi_sysset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `ims_sz_yi_virtual_category`
--
ALTER TABLE `ims_sz_yi_virtual_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_virtual_data`
--
ALTER TABLE `ims_sz_yi_virtual_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ims_sz_yi_virtual_type`
--
ALTER TABLE `ims_sz_yi_virtual_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
