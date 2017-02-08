-- phpMyAdmin SQL Dump
-- version 4.4.15.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2017-02-08 16:01:43
-- 服务器版本： 5.5.54-log
-- PHP Version: 5.4.45

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `srk_boot`
--

-- --------------------------------------------------------

--
-- 表的结构 `srk_api`
--

CREATE TABLE IF NOT EXISTS `srk_api` (
  `id` int(11) NOT NULL COMMENT 'id号',
  `uid` int(11) NOT NULL COMMENT '用户的id号',
  `api_key` varchar(32) COLLATE utf8mb4_bin NOT NULL COMMENT '密钥',
  `expiration` int(11) NOT NULL COMMENT '到期时间',
  `status` int(11) NOT NULL COMMENT '0停用1正常2禁止'
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 转存表中的数据 `srk_api`
--

INSERT INTO `srk_api` (`id`, `uid`, `api_key`, `expiration`, `status`) VALUES


-- --------------------------------------------------------

--
-- 表的结构 `srk_article`
--

CREATE TABLE IF NOT EXISTS `srk_article` (
  `id` int(11) NOT NULL COMMENT 'id',
  `title` varchar(64) COLLATE utf8mb4_bin NOT NULL COMMENT '文章名',
  `content` mediumtext COLLATE utf8mb4_bin NOT NULL COMMENT '内容',
  `author` varchar(32) COLLATE utf8mb4_bin NOT NULL COMMENT '作者',
  `time` int(11) NOT NULL COMMENT '时间',
  `type` int(11) DEFAULT '1' COMMENT '0隐藏1普通2顶置'
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 转存表中的数据 `srk_article`
--

INSERT INTO `srk_article` (`id`, `title`, `content`, `author`, `time`, `type`) VALUES
(2, '购买说明', '<p>2017-02-05开始进行支付测试,所有套餐价格在0.1~0.5元人民币之间。<br/></p>', 'shirakun', 1486347706, 1),
(5, '公测说明', '<p>2017-02-06增加7层攻击和停止攻击操作，本程序现存在很多bug，如果您发现了一个bug请及时联系我。</p><p>api权限现在无法正常使用，请大家注意！</p>', 'shirakun', 1486347895, 1),
(6, '用户须知', '<p>公测随时可能结束，结束时将删除所有的用户数据(但我们可能保留攻击记录),公测时随时可能删除一些数据，请不要滥用，滥用者将永远禁止使用。</p>', 'shirakun', 1485680020, 1),
(7, '目前支持的攻击模式', '<p>目前仅有4层的ntp,7层的xmlrpc可以使用,ntp有vip节点,xmlrpc只有普通节点.</p>', 'shirakun', 1486347759, 2),
(9, '现已添加7层攻击', '<p>2017-02-06添加7层攻击,支持对http/https发动攻击,对查询量大的url发动cc攻击效果更佳.</p>', 'shirakun', 1486540664, 1),

-- --------------------------------------------------------

--
-- 表的结构 `srk_cdkey`
--

CREATE TABLE IF NOT EXISTS `srk_cdkey` (
  `id` int(11) NOT NULL COMMENT 'id号',
  `value` varchar(32) COLLATE utf8mb4_bin NOT NULL COMMENT '卡密',
  `plan` int(11) DEFAULT NULL COMMENT '对应套餐号',
  `expiration` int(11) DEFAULT NULL,
  `type` int(11) NOT NULL DEFAULT '1' COMMENT '1普通套餐2临时补充包3套餐补充包4api权限5并发补充包',
  `status` int(11) NOT NULL COMMENT '状态',
  `expiry_date` int(11) DEFAULT NULL COMMENT '有效期',
  `num` int(11) DEFAULT NULL COMMENT '临时或套餐补充包的补充数量',
  `uid` int(11) DEFAULT NULL COMMENT '使用者id',
  `use_time` int(11) DEFAULT NULL COMMENT '使用时间',
  `note` varchar(64) COLLATE utf8mb4_bin DEFAULT '没有备注' COMMENT '备注'
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 转存表中的数据 `srk_cdkey`
--

INSERT INTO `srk_cdkey` (`id`, `value`, `plan`, `expiration`, `type`, `status`, `expiry_date`, `num`, `uid`, `use_time`, `note`) VALUES
(40, 'fdb8ee71a77d5901b0f7eb032b483497', 2, 259200, 1, 0, NULL, NULL, NULL, NULL, '没有备注'),
(43, '04cf3167058817f962a3095d2ce61d44', 2, 259200, 1, 1, NULL, NULL, 2, 1486279414, '没有备注');

-- --------------------------------------------------------

--
-- 表的结构 `srk_config`
--

CREATE TABLE IF NOT EXISTS `srk_config` (
  `name` varchar(32) COLLATE utf8mb4_bin NOT NULL COMMENT '名称',
  `value` varchar(320) COLLATE utf8mb4_bin NOT NULL COMMENT '值',
  `note` varchar(32) COLLATE utf8mb4_bin NOT NULL COMMENT '备注'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 转存表中的数据 `srk_config`
--

INSERT INTO `srk_config` (`name`, `value`, `note`) VALUES
('reflect', '["ntp","dns","ssdp","mssql","chargen","snmp","sentinel","netbios","ts3","db2","portmap"]', '反射类型的模式'),
('usually', '["udp","vse","telnet","home","tcp","tcp-se","tcp-ack","tcp-rst","tcp-psh","tcp-fin","tcp-xmas","wizard","dominate","zap","ssyn","essyn","issyn","xsyn","zsyn","csyn"]', '通常类型的模式'),
('cron_user', '1486532758', '上一次更新用户数据执行的时间'),
('api_buy', '0', '是否可以购买api'),
('application', '["xmlrpc"]', '7层攻击模式');

-- --------------------------------------------------------

--
-- 表的结构 `srk_history`
--

CREATE TABLE IF NOT EXISTS `srk_history` (
  `id` int(11) NOT NULL COMMENT 'id号',
  `ip` varchar(320) COLLATE utf8mb4_bin NOT NULL COMMENT '目标ip',
  `mode` varchar(16) COLLATE utf8mb4_bin NOT NULL COMMENT '攻击模式',
  `time` int(11) NOT NULL COMMENT '攻击时间',
  `start_time` int(11) NOT NULL COMMENT '开始时间',
  `uid` int(11) NOT NULL COMMENT '发起攻击的会员id',
  `stop` int(11) NOT NULL COMMENT '是否人工停止',
  `server_id` int(11) DEFAULT NULL COMMENT '执行任务的节点id'
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 转存表中的数据 `srk_history`
--

INSERT INTO `srk_history` (`id`, `ip`, `mode`, `time`, `start_time`, `uid`, `stop`, `server_id`) VALUES
(1, '8.8.8.8', 'ntp', 300, 1481251726, 2, 0, 1),
(2, '8.8.4.4', 'ssdp', 600, 1481251526, 2, 1, 1),
(4, '4.4.4.4', 'dns', 36000, 1481254203, 2, 0, 1),


-- --------------------------------------------------------

--
-- 表的结构 `srk_order`
--

CREATE TABLE IF NOT EXISTS `srk_order` (
  `id` int(11) NOT NULL COMMENT '订单编号',
  `uid` int(11) NOT NULL COMMENT '会员id',
  `pay_total` varchar(32) NOT NULL COMMENT '支付金额',
  `plan_id` varchar(32) DEFAULT NULL COMMENT '套餐id',
  `pay_type` varchar(32) NOT NULL COMMENT '支付类型',
  `pay_sn` varchar(32) DEFAULT NULL COMMENT '支付编号',
  `pay_status` int(11) NOT NULL COMMENT '支付状态0未支付1已支付',
  `time` int(11) NOT NULL COMMENT '订单创建时间',
  `pay_time` int(11) DEFAULT NULL COMMENT '订单支付时间'
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `srk_order`
--

INSERT INTO `srk_order` (`id`, `uid`, `pay_total`, `plan_id`, `pay_type`, `pay_sn`, `pay_status`, `time`, `pay_time`) VALUES



-- --------------------------------------------------------

--
-- 表的结构 `srk_plans`
--

CREATE TABLE IF NOT EXISTS `srk_plans` (
  `id` int(11) NOT NULL COMMENT 'id号',
  `name` varchar(16) COLLATE utf8mb4_bin NOT NULL COMMENT '名称',
  `concern` varchar(300) COLLATE utf8mb4_bin DEFAULT '此套餐暂无简介' COMMENT '简介',
  `price` float NOT NULL COMMENT '价格',
  `type` int(11) DEFAULT '1' COMMENT '1普通2临时3套餐4api5并发',
  `cycle` int(11) DEFAULT NULL COMMENT '付费周期(秒)',
  `maxtime` int(11) DEFAULT NULL COMMENT '最大时间',
  `maxnum` int(11) NOT NULL COMMENT '每日次数',
  `maxboot` int(11) NOT NULL DEFAULT '1' COMMENT '最大并发',
  `status` int(11) NOT NULL COMMENT '状态',
  `vip` int(11) NOT NULL COMMENT '是否为vip'
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 转存表中的数据 `srk_plans`
--

INSERT INTO `srk_plans` (`id`, `name`, `concern`, `price`, `type`, `cycle`, `maxtime`, `maxnum`, `maxboot`, `status`, `vip`) VALUES
(1, 'admin2', '这是管理员专属套餐普通会员禁止购买', 999, 1, 2592000, 3600, 100, 1, 0, 1),
(2, 'smaill', '这是一个小型套餐', 0.1, 1, 86400, 60, 20, 1, 1, 0),
(3, 'vip测试套餐', '此套餐暂无简介', 0.2, 1, 86400, 60, 15, 1, 1, 1);

-- --------------------------------------------------------

--
-- 表的结构 `srk_server`
--

CREATE TABLE IF NOT EXISTS `srk_server` (
  `id` int(11) NOT NULL COMMENT 'id号',
  `name` varchar(32) COLLATE utf8mb4_bin NOT NULL COMMENT '节点名称',
  `type` int(11) DEFAULT '1' COMMENT '节点类型',
  `vip` int(11) DEFAULT '0' COMMENT '是否为vip节点',
  `host` varchar(320) COLLATE utf8mb4_bin NOT NULL COMMENT '节点ip或api路径',
  `port` int(11) DEFAULT NULL,
  `username` varchar(32) COLLATE utf8mb4_bin DEFAULT 'root' COMMENT '节点登录用户名',
  `passwd` varchar(32) COLLATE utf8mb4_bin NOT NULL COMMENT '节点或api密码',
  `tool_dir` varchar(320) COLLATE utf8mb4_bin DEFAULT '/root/ddos' COMMENT 'ddos工具的路径(最后一位不带/)',
  `mode` varchar(320) COLLATE utf8mb4_bin NOT NULL COMMENT '节点接受的类型(json)',
  `maximum` int(11) DEFAULT '10' COMMENT '最大并发',
  `note` varchar(320) COLLATE utf8mb4_bin DEFAULT '没有备注' COMMENT '备注'
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 转存表中的数据 `srk_server`
--

INSERT INTO `srk_server` (`id`, `name`, `type`, `vip`, `host`, `port`, `username`, `passwd`, `tool_dir`, `mode`, `maximum`, `note`) VALUES
(4, '普通输出-1', 1, 0, '127.0.0.1', 22, 'root', '123123', '/root', '["ntp","ssdp"]', 3, '美国'),
(5, 'vip-1', 1, 1, '127.0.0.1', 22, 'root', '123123', '/root', '["ntp","ssdp"]', 2, '大水管限流量'),
(6, 'host1plus', 1, 0, '127.0.0.1', 22, 'root', '123123', '/root', '["xmlrpc"]', 2, 'host1plus');

-- --------------------------------------------------------

--
-- 表的结构 `srk_users`
--

CREATE TABLE IF NOT EXISTS `srk_users` (
  `id` int(11) NOT NULL COMMENT '用户id',
  `username` varchar(16) COLLATE utf8mb4_bin NOT NULL COMMENT '用户名',
  `passwd` varchar(32) COLLATE utf8mb4_bin NOT NULL COMMENT '密码',
  `email` varchar(320) COLLATE utf8mb4_bin NOT NULL COMMENT '邮箱',
  `register_time` int(11) NOT NULL COMMENT '注册时间',
  `status` int(11) NOT NULL COMMENT '用户状态',
  `type` int(11) NOT NULL COMMENT '用户类型',
  `plan` int(11) NOT NULL COMMENT '所属套餐',
  `plan_name` varchar(32) COLLATE utf8mb4_bin DEFAULT '未购买' COMMENT '套餐名称',
  `maxtime` int(11) NOT NULL DEFAULT '0' COMMENT '最大攻击时间',
  `maxnum` int(11) NOT NULL COMMENT '最大次数',
  `maxboot` int(11) NOT NULL DEFAULT '0' COMMENT '最大并发',
  `remainder` int(11) NOT NULL COMMENT '剩余次数',
  `expiration` int(11) NOT NULL COMMENT '到期时间',
  `vip` int(11) NOT NULL COMMENT '是否为vip用户'
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 转存表中的数据 `srk_users`
--

INSERT INTO `srk_users` (`id`, `username`, `passwd`, `email`, `register_time`, `status`, `type`, `plan`, `plan_name`, `maxtime`, `maxnum`, `maxboot`, `remainder`, `expiration`, `vip`) VALUES
(2, 'shirakun', '3788a82efc8f82aaecf6b24ba7718dbf', 'test@test.com', 1481185914, 1, 2, 2, 'smaill', 60, 20, 1, 20, 1487577581, 0),

-- --------------------------------------------------------

--
-- 表的结构 `srk_verification_code`
--

CREATE TABLE IF NOT EXISTS `srk_verification_code` (
  `id` int(11) NOT NULL COMMENT 'id号',
  `uid` int(11) NOT NULL COMMENT '对应用户的id',
  `value` varchar(32) COLLATE utf8mb4_bin NOT NULL COMMENT '验证码',
  `email` varchar(320) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '用户邮箱',
  `type` int(11) NOT NULL COMMENT '1激活2更换邮箱3修改密码',
  `expiration` int(11) NOT NULL COMMENT '到期时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- 表的结构 `srk_white_list`
--

CREATE TABLE IF NOT EXISTS `srk_white_list` (
  `id` int(11) NOT NULL COMMENT 'id号',
  `ip` varchar(32) COLLATE utf8mb4_bin NOT NULL COMMENT 'ip',
  `note` varchar(64) COLLATE utf8mb4_bin DEFAULT '没有备注' COMMENT '备注',
  `time` int(11) NOT NULL COMMENT '添加时间'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 转存表中的数据 `srk_white_list`
--

INSERT INTO `srk_white_list` (`id`, `ip`, `note`, `time`) VALUES
(1, '127.0.0.1', '', 1485742031);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `srk_api`
--
ALTER TABLE `srk_api`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `srk_article`
--
ALTER TABLE `srk_article`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `srk_cdkey`
--
ALTER TABLE `srk_cdkey`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `srk_history`
--
ALTER TABLE `srk_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `srk_order`
--
ALTER TABLE `srk_order`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `srk_plans`
--
ALTER TABLE `srk_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `srk_server`
--
ALTER TABLE `srk_server`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `srk_users`
--
ALTER TABLE `srk_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `srk_verification_code`
--
ALTER TABLE `srk_verification_code`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `srk_white_list`
--
ALTER TABLE `srk_white_list`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `srk_api`
--
ALTER TABLE `srk_api`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id号',AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `srk_article`
--
ALTER TABLE `srk_article`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `srk_cdkey`
--
ALTER TABLE `srk_cdkey`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id号',AUTO_INCREMENT=44;
--
-- AUTO_INCREMENT for table `srk_history`
--
ALTER TABLE `srk_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id号',AUTO_INCREMENT=41;
--
-- AUTO_INCREMENT for table `srk_order`
--
ALTER TABLE `srk_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '订单编号',AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT for table `srk_plans`
--
ALTER TABLE `srk_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id号',AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `srk_server`
--
ALTER TABLE `srk_server`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id号',AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `srk_users`
--
ALTER TABLE `srk_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户id',AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `srk_verification_code`
--
ALTER TABLE `srk_verification_code`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id号';
--
-- AUTO_INCREMENT for table `srk_white_list`
--
ALTER TABLE `srk_white_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id号',AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
