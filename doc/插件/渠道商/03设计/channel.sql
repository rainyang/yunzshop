SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `mydb` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `mydb` ;

-- -----------------------------------------------------
-- Table `mydb`.`ims_sz_yi_channel_level`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`ims_sz_yi_channel_level` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `level_name` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL COMMENT '等级名称',
  `level_num` INT(1) NULL COMMENT '等级权重',
  `purchase_discount` VARCHAR(45) NULL COMMENT '进货折扣 %',
  `min_price` DECIMAL(10,2) NULL COMMENT '最小进货量',
  `profit_sharing` VARCHAR(45) NULL COMMENT '利润分成\n%',
  `become` INT(11) NULL COMMENT '升级条件',
  `goods_id` INT(11) NULL COMMENT '指定商品id',
  `createtime` INT(11) NULL COMMENT '创建时间',
  `updatetime` INT(11) NULL COMMENT '更新时间',
  PRIMARY KEY (`id`))
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = '渠道商等级';


-- -----------------------------------------------------
-- Table `mydb`.`ims_sz_yi_member`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`ims_sz_yi_member` (
  `id` INT(11) NOT NULL,
  `uniacid` INT(11) NULL DEFAULT '0',
  `uid` INT(11) NULL DEFAULT '0',
  `groupid` INT(11) NULL DEFAULT '0',
  `level` INT(11) NULL DEFAULT '0',
  `agentid` INT(11) NULL DEFAULT '0',
  `openid` VARCHAR(50) NULL DEFAULT '',
  `realname` VARCHAR(20) NULL DEFAULT '',
  `mobile` VARCHAR(11) NULL DEFAULT '',
  `pwd` VARCHAR(100) NULL DEFAULT NULL,
  `weixin` VARCHAR(100) NULL DEFAULT '',
  `content` TEXT NULL DEFAULT NULL,
  `createtime` INT(10) NULL DEFAULT '0',
  `agenttime` INT(10) NULL DEFAULT '0',
  `status` TINYINT(1) NULL DEFAULT '0',
  `isagent` TINYINT(1) NULL DEFAULT '0',
  `clickcount` INT(11) NULL DEFAULT '0',
  `agentlevel` INT(11) NULL DEFAULT '0',
  `bonuslevel` INT(11) NULL DEFAULT '0',
  `bonus_area` TINYINT(1) NULL DEFAULT '0',
  `bonus_province` VARCHAR(50) NULL DEFAULT '',
  `bonus_city` VARCHAR(50) NULL DEFAULT '',
  `bonus_district` VARCHAR(50) NULL DEFAULT '',
  `bonus_area_commission` DECIMAL(10,2) NULL DEFAULT '0.00',
  `bonus_status` TINYINT(1) NULL DEFAULT '0',
  `noticeset` TEXT NULL DEFAULT NULL,
  `nickname` VARCHAR(255) NULL DEFAULT '',
  `credit1` INT(11) NULL DEFAULT '0',
  `credit2` DECIMAL(10,2) NULL DEFAULT '0.00',
  `birthyear` VARCHAR(255) NULL DEFAULT '',
  `birthmonth` VARCHAR(255) NULL DEFAULT '',
  `birthday` VARCHAR(255) NULL DEFAULT '',
  `gender` TINYINT(3) NULL DEFAULT '0',
  `avatar` VARCHAR(255) NULL DEFAULT '',
  `province` VARCHAR(255) NULL DEFAULT '',
  `city` VARCHAR(255) NULL DEFAULT '',
  `area` VARCHAR(255) NULL DEFAULT '',
  `childtime` INT(11) NULL DEFAULT '0',
  `inviter` INT(11) NULL DEFAULT '0',
  `agentnotupgrade` TINYINT(3) NULL DEFAULT '0',
  `agentselectgoods` TINYINT(3) NULL DEFAULT '0',
  `agentblack` TINYINT(3) NULL DEFAULT '0',
  `fixagentid` TINYINT(3) NULL DEFAULT '0',
  `regtype` TINYINT(3) NULL DEFAULT '1',
  `isbindmobile` TINYINT(3) NULL DEFAULT '0',
  `isjumpbind` TINYINT(3) NULL DEFAULT '0',
  `diymemberid` INT(11) NULL DEFAULT '0',
  `diymemberdataid` INT(11) NULL DEFAULT '0',
  `diycommissionid` INT(11) NULL DEFAULT '0',
  `diycommissiondataid` INT(11) NULL DEFAULT '0',
  `diymemberfields` TEXT NULL DEFAULT NULL,
  `diymemberdata` TEXT NULL DEFAULT NULL,
  `diycommissionfields` TEXT NULL DEFAULT NULL,
  `diycommissiondata` TEXT NULL DEFAULT NULL,
  `isblack` TINYINT(3) NULL DEFAULT '0',
  `referralsn` VARCHAR(255) NOT NULL,
  `bindapp` TINYINT(4) NOT NULL DEFAULT '0',
  `ordersn_general` VARCHAR(255) NOT NULL DEFAULT '',
  `ischannel` INT(1) NULL COMMENT '是否是渠道商',
  `channel_level` INT(1) NULL COMMENT '渠道商等级',
  `channeltime` INT(11) NULL COMMENT '成为渠道商时间',
  PRIMARY KEY (`id`),
  INDEX `idx_uniacid` (`uniacid` ASC),
  INDEX `idx_shareid` (`agentid` ASC),
  INDEX `idx_openid` (`openid` ASC),
  INDEX `idx_status` (`status` ASC),
  INDEX `idx_agenttime` (`agenttime` ASC),
  INDEX `idx_isagent` (`isagent` ASC),
  INDEX `idx_uid` (`uid` ASC),
  INDEX `idx_groupid` (`groupid` ASC),
  INDEX `idx_level` (`level` ASC))
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = '芸众商城会员表';


-- -----------------------------------------------------
-- Table `mydb`.`ims_sz_yi_af_channel`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`ims_sz_yi_af_channel` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `uniacid` INT(11) NOT NULL,
  `openid` VARCHAR(50) NULL,
  `realname` VARCHAR(45) NOT NULL COMMENT '真实姓名',
  `mobile` VARCHAR(11) NULL COMMENT '电话号',
  `diychannelid` INT(11) NULL,
  `diychanneldataid` INT(11) NULL,
  `diychannelfields` TEXT NULL,
  `diychanneldata` TEXT NULL,
  PRIMARY KEY (`id`))
ENGINE = MyISAM
COMMENT = '会员申请渠道商';


-- -----------------------------------------------------
-- Table `mydb`.`ims_sz_yi_channel_apply`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`ims_sz_yi_channel_apply` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `uniacid` INT(11) NOT NULL,
  `openid` VARCHAR(50) NULL,
  `apply_money` DECIMAL(10,2) NULL COMMENT '申请金额',
  `apply_time` INT(11) NULL COMMENT '申请时间',
  `type` TINYINT(2) NULL COMMENT '提现类型',
  `status` TINYINT(2) NULL COMMENT '申请状态',
  `finish_time` INT(11) NULL COMMENT '完成时间',
  PRIMARY KEY (`id`))
ENGINE = MyISAM
COMMENT = '渠道商申请提现';


-- -----------------------------------------------------
-- Table `mydb`.`ims_sz_yi_channel_stock`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`ims_sz_yi_channel_stock` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `uniacid` INT(11) NOT NULL,
  `openid` VARCHAR(50) NULL,
  `goodsid` INT(11) NOT NULL COMMENT '商品ID',
  `stock_total` INT(11) NOT NULL COMMENT '库存总数',
  PRIMARY KEY (`id`))
ENGINE = MyISAM
COMMENT = '渠道商库存';


-- -----------------------------------------------------
-- Table `mydb`.`ims_sz_yi_channel_stock_log`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`ims_sz_yi_channel_stock_log` (
  `id` INT(11) NOT NULL,
  `uniacid` INT(11) NOT NULL,
  `openid` VARCHAR(50) NULL,
  `goodsid` INT(11) NULL COMMENT '商品ID',
  `every_turn` INT(11) NULL COMMENT '每次进货量',
  `every_turn_price` DECIMAL(10,2) NULL COMMENT '每次进货单价',
  `every_turn_discount` DECIMAL(10,2) NULL COMMENT '每次进货当前折扣',
  `goods_price` DECIMAL(10,2) NULL COMMENT '进货时商品单价',
  PRIMARY KEY (`id`))
ENGINE = MyISAM
COMMENT = '渠道商进货记录';


-- -----------------------------------------------------
-- Table `mydb`.`ims_sz_yi_order`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`ims_sz_yi_order` (
  `id` INT(11) NOT NULL,
  `uniacid` INT(11) NULL DEFAULT '0',
  `openid` VARCHAR(50) NULL DEFAULT '',
  `agentid` INT(11) NULL DEFAULT '0',
  `ordersn` VARCHAR(20) NULL DEFAULT '',
  `price` DECIMAL(10,2) NULL DEFAULT '0.00',
  `goodsprice` DECIMAL(10,2) NULL DEFAULT '0.00',
  `discountprice` DECIMAL(10,2) NULL DEFAULT '0.00',
  `status` TINYINT(4) NULL DEFAULT '0' COMMENT '-1取消状态，0普通状态，1为已付款，2为已发货，3为成功',
  `paytype` TINYINT(1) NULL DEFAULT '0' COMMENT '1为余额，2为在线，3为到付',
  `transid` VARCHAR(30) NULL DEFAULT '0' COMMENT '微信支付单号',
  `remark` VARCHAR(1000) NULL DEFAULT '',
  `addressid` INT(11) NULL DEFAULT '0',
  `dispatchprice` DECIMAL(10,2) NULL DEFAULT '0.00',
  `dispatchid` INT(10) NULL DEFAULT '0',
  `createtime` INT(10) NULL DEFAULT NULL,
  `dispatchtype` TINYINT(3) NULL DEFAULT '0',
  `carrier` TEXT NULL DEFAULT NULL,
  `refundid` INT(11) NULL DEFAULT '0',
  `iscomment` TINYINT(3) NULL DEFAULT '0',
  `creditadd` TINYINT(3) NULL DEFAULT '0',
  `deleted` TINYINT(3) NULL DEFAULT '0',
  `userdeleted` TINYINT(3) NULL DEFAULT '0',
  `finishtime` INT(11) NULL DEFAULT '0',
  `paytime` INT(11) NULL DEFAULT '0',
  `expresscom` VARCHAR(30) NOT NULL DEFAULT '',
  `expresssn` VARCHAR(50) NOT NULL DEFAULT '',
  `express` VARCHAR(255) NULL DEFAULT '',
  `sendtime` INT(11) NULL DEFAULT '0',
  `fetchtime` INT(11) NULL DEFAULT '0',
  `cash` TINYINT(3) NULL DEFAULT '0',
  `canceltime` INT(11) NULL DEFAULT NULL,
  `cancelpaytime` INT(11) NULL DEFAULT '0',
  `refundtime` INT(11) NULL DEFAULT '0',
  `isverify` TINYINT(3) NULL DEFAULT '0',
  `verified` TINYINT(3) NULL DEFAULT '0',
  `verifyopenid` VARCHAR(255) NULL DEFAULT '',
  `verifycode` TEXT NULL DEFAULT NULL,
  `verifytime` INT(11) NULL DEFAULT '0',
  `verifystoreid` INT(11) NULL DEFAULT '0',
  `deductprice` DECIMAL(10,2) NULL DEFAULT '0.00',
  `deductcredit` INT(11) NULL DEFAULT '0',
  `deductcredit2` DECIMAL(10,2) NULL DEFAULT '0.00',
  `deductenough` DECIMAL(10,2) NULL DEFAULT '0.00',
  `virtual` INT(11) NULL DEFAULT '0',
  `virtual_info` TEXT NULL DEFAULT NULL,
  `virtual_str` TEXT NULL DEFAULT NULL,
  `address` TEXT NULL DEFAULT NULL,
  `sysdeleted` TINYINT(3) NULL DEFAULT '0',
  `ordersn2` INT(11) NULL DEFAULT '0',
  `changeprice` DECIMAL(10,2) NULL DEFAULT '0.00',
  `changedispatchprice` DECIMAL(10,2) NULL DEFAULT '0.00',
  `oldprice` DECIMAL(10,2) NULL DEFAULT '0.00',
  `olddispatchprice` DECIMAL(10,2) NULL DEFAULT '0.00',
  `isvirtual` TINYINT(3) NULL DEFAULT '0',
  `couponid` INT(11) NULL DEFAULT '0',
  `couponprice` DECIMAL(10,2) NULL DEFAULT '0.00',
  `supplier_uid` INT(11) NOT NULL COMMENT '供应商ID',
  `supplier_apply_status` TINYINT(3) NOT NULL,
  `printstate` TINYINT(3) NULL DEFAULT '0',
  `printstate2` TINYINT(3) NULL DEFAULT '0',
  `diyformid` INT(11) NULL DEFAULT '0',
  `diyformdata` TEXT NULL DEFAULT NULL,
  `diyformfields` TEXT NULL DEFAULT NULL,
  `storeid` INT(11) NOT NULL DEFAULT '0',
  `refundstate` TINYINT(3) NULL DEFAULT '0',
  `redprice` VARCHAR(50) NULL DEFAULT '',
  `merchant_apply_status` TINYINT(4) NOT NULL,
  `redstatus` VARCHAR(100) NULL DEFAULT '',
  `cashier` TINYINT(1) NULL DEFAULT '0',
  `realprice` DECIMAL(10,0) NULL DEFAULT '0',
  `deredpack` TINYINT(1) NULL DEFAULT '0',
  `decommission` TINYINT(1) NULL DEFAULT '0',
  `decredits` TINYINT(1) NULL DEFAULT '0',
  `cashierid` INT(11) NULL DEFAULT '0',
  `ischannelself` INT(1) NULL COMMENT '是否渠道商自提订单',
  PRIMARY KEY (`id`),
  INDEX `idx_uniacid` (`uniacid` ASC),
  INDEX `idx_openid` (`openid` ASC),
  INDEX `idx_shareid` (`agentid` ASC),
  INDEX `idx_status` (`status` ASC),
  INDEX `idx_createtime` (`createtime` ASC),
  INDEX `idx_refundid` (`refundid` ASC),
  INDEX `idx_paytime` (`paytime` ASC),
  INDEX `idx_finishtime` (`finishtime` ASC))
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mydb`.`ims_sz_yi_order_goods`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`ims_sz_yi_order_goods` (
  `id` INT(11) NOT NULL,
  `openid` VARCHAR(255) NULL DEFAULT NULL,
  `uniacid` INT(11) NULL DEFAULT '0',
  `orderid` INT(11) NULL DEFAULT '0',
  `goodsid` INT(11) NULL DEFAULT '0',
  `price` DECIMAL(10,2) NULL DEFAULT '0.00',
  `total` INT(11) NULL DEFAULT '1',
  `optionid` INT(10) NULL DEFAULT '0',
  `createtime` INT(11) NULL DEFAULT '0',
  `optionname` TEXT NULL DEFAULT NULL,
  `commission1` TEXT NULL DEFAULT NULL COMMENT '0',
  `applytime1` INT(11) NULL DEFAULT '0',
  `checktime1` INT(10) NULL DEFAULT '0',
  `paytime1` INT(11) NULL DEFAULT '0',
  `invalidtime1` INT(11) NULL DEFAULT '0',
  `deletetime1` INT(11) NULL DEFAULT '0',
  `status1` TINYINT(3) NULL DEFAULT '0' COMMENT '申请状态，-2删除，-1无效，0未申请，1申请，2审核通过 3已打款',
  `content1` TEXT NULL DEFAULT NULL,
  `commission2` TEXT NULL DEFAULT NULL,
  `applytime2` INT(11) NULL DEFAULT '0',
  `checktime2` INT(10) NULL DEFAULT '0',
  `paytime2` INT(11) NULL DEFAULT '0',
  `invalidtime2` INT(11) NULL DEFAULT '0',
  `deletetime2` INT(11) NULL DEFAULT '0',
  `status2` TINYINT(3) NULL DEFAULT '0' COMMENT '申请状态，-2删除，-1无效，0未申请，1申请，2审核通过 3已打款',
  `content2` TEXT NULL DEFAULT NULL,
  `commission3` TEXT NULL DEFAULT NULL,
  `applytime3` INT(11) NULL DEFAULT '0',
  `checktime3` INT(10) NULL DEFAULT '0',
  `paytime3` INT(11) NULL DEFAULT '0',
  `invalidtime3` INT(11) NULL DEFAULT '0',
  `deletetime3` INT(11) NULL DEFAULT '0',
  `status3` TINYINT(3) NULL DEFAULT '0' COMMENT '申请状态，-2删除，-1无效，0未申请，1申请，2审核通过 3已打款',
  `content3` TEXT NULL DEFAULT NULL,
  `realprice` DECIMAL(10,2) NULL DEFAULT '0.00',
  `goodssn` VARCHAR(255) NULL DEFAULT '',
  `productsn` VARCHAR(255) NULL DEFAULT '',
  `nocommission` TINYINT(3) NULL DEFAULT '0',
  `changeprice` DECIMAL(10,2) NULL DEFAULT '0.00',
  `oldprice` DECIMAL(10,2) NULL DEFAULT '0.00',
  `commissions` TEXT NULL DEFAULT NULL,
  `supplier_uid` INT(11) NOT NULL COMMENT '供应商ID',
  `supplier_apply_status` TINYINT(4) NOT NULL COMMENT '1为供应商已提现',
  `printstate` TINYINT(3) NULL DEFAULT '0',
  `printstate2` TINYINT(3) NULL DEFAULT '0',
  `diyformdataid` INT(11) NULL DEFAULT '0',
  `diyformid` INT(11) NULL DEFAULT '0',
  `diyformdata` TEXT NULL DEFAULT NULL,
  `diyformfields` TEXT NULL DEFAULT NULL,
  `goods_op_cost_price` DECIMAL(10,2) NOT NULL,
  `channel_id` INT(11) NULL COMMENT '渠道商id',
  PRIMARY KEY (`id`),
  INDEX `idx_uniacid` (`uniacid` ASC),
  INDEX `idx_orderid` (`orderid` ASC),
  INDEX `idx_goodsid` (`goodsid` ASC),
  INDEX `idx_createtime` (`createtime` ASC),
  INDEX `idx_applytime1` (`applytime1` ASC),
  INDEX `idx_checktime1` (`checktime1` ASC),
  INDEX `idx_status1` (`status1` ASC),
  INDEX `idx_applytime2` (`applytime2` ASC),
  INDEX `idx_checktime2` (`checktime2` ASC),
  INDEX `idx_status2` (`status2` ASC),
  INDEX `idx_applytime3` (`applytime3` ASC),
  INDEX `idx_invalidtime1` (`invalidtime1` ASC),
  INDEX `idx_checktime3` (`checktime3` ASC),
  INDEX `idx_invalidtime2` (`invalidtime2` ASC),
  INDEX `idx_invalidtime3` (`invalidtime3` ASC),
  INDEX `idx_status3` (`status3` ASC),
  INDEX `idx_paytime1` (`paytime1` ASC),
  INDEX `idx_paytime2` (`paytime2` ASC),
  INDEX `idx_paytime3` (`paytime3` ASC))
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
