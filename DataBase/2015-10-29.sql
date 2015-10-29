/*
SQLyog 企业版 - MySQL GUI v8.14 
MySQL - 5.6.24 : Database - pxpark_db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`pxpark_db` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `pxpark_db`;

/*Table structure for table ` px_evaluate` */

DROP TABLE IF EXISTS ` px_evaluate`;

CREATE TABLE ` px_evaluate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parkRecord_id` int(11) NOT NULL COMMENT '停车记录id',
  `user_id` int(11) NOT NULL COMMENT '被评价者的id',
  `content` varchar(45) DEFAULT NULL COMMENT '评价内容',
  `level` int(11) DEFAULT NULL COMMENT '评价等级\n1差\n2中\n3好',
  `time` datetime DEFAULT NULL COMMENT '评价时间',
  PRIMARY KEY (`id`,`parkRecord_id`,`user_id`),
  KEY `fk_ evaluate_px_parkRecord1_idx` (`parkRecord_id`),
  KEY `fk_ evaluate_px_user1_idx` (`user_id`),
  CONSTRAINT `fk_ evaluate_px_parkRecord1` FOREIGN KEY (`parkRecord_id`) REFERENCES `px_parkrecord` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ evaluate_px_user1` FOREIGN KEY (`user_id`) REFERENCES `px_user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table ` px_evaluate` */

/*Table structure for table `pricay` */

DROP TABLE IF EXISTS `pricay`;

CREATE TABLE `pricay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text COMMENT '条款文档',
  `time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `pricay` */

/*Table structure for table `px_appointment` */

DROP TABLE IF EXISTS `px_appointment`;

CREATE TABLE `px_appointment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `privatePark_id` int(11) NOT NULL,
  `start_time` datetime DEFAULT NULL COMMENT '开始时间',
  `end_time` datetime DEFAULT NULL COMMENT '结束时间',
  PRIMARY KEY (`id`,`user_id`,`privatePark_id`),
  KEY `fk_px_appointment_px_user1_idx` (`user_id`),
  KEY `fk_px_appointment_px_privatePark1_idx` (`privatePark_id`),
  CONSTRAINT `fk_px_appointment_px_privatePark1` FOREIGN KEY (`privatePark_id`) REFERENCES `px_privatepark` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_px_appointment_px_user1` FOREIGN KEY (`user_id`) REFERENCES `px_user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `px_appointment` */

/*Table structure for table `px_captcha` */

DROP TABLE IF EXISTS `px_captcha`;

CREATE TABLE `px_captcha` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `captcha` varchar(45) DEFAULT NULL COMMENT '验证码',
  `user_id` int(11) DEFAULT NULL,
  `time` datetime DEFAULT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `px_captcha` */

/*Table structure for table `px_car` */

DROP TABLE IF EXISTS `px_car`;

CREATE TABLE `px_car` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no` varchar(45) DEFAULT NULL COMMENT '车牌号',
  `type` varchar(45) DEFAULT NULL COMMENT '类型\n1小车\n2大车\n',
  `color` varchar(45) DEFAULT NULL COMMENT '颜色',
  `brand_id` int(11) DEFAULT NULL COMMENT '品牌id',
  `model_id` int(11) DEFAULT NULL COMMENT '类型id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `px_car` */

insert  into `px_car`(`id`,`no`,`type`,`color`,`brand_id`,`model_id`) values (1,'鲁B123','1',NULL,NULL,NULL);

/*Table structure for table `px_collect` */

DROP TABLE IF EXISTS `px_collect`;

CREATE TABLE `px_collect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `park_id` int(11) NOT NULL,
  `privatePark_id` int(11) NOT NULL,
  PRIMARY KEY (`id`,`user_id`,`park_id`,`privatePark_id`),
  KEY `fk_px_collect_px_user1_idx` (`user_id`),
  KEY `fk_px_collect_px_park1_idx` (`park_id`),
  KEY `fk_px_collect_px_privatePark1_idx` (`privatePark_id`),
  CONSTRAINT `fk_px_collect_px_park1` FOREIGN KEY (`park_id`) REFERENCES `px_park` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_px_collect_px_privatePark1` FOREIGN KEY (`privatePark_id`) REFERENCES `px_privatepark` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_px_collect_px_user1` FOREIGN KEY (`user_id`) REFERENCES `px_user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `px_collect` */

/*Table structure for table `px_park` */

DROP TABLE IF EXISTS `px_park`;

CREATE TABLE `px_park` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `total_num` int(11) DEFAULT NULL COMMENT '停车位总数',
  `remain_num` int(11) DEFAULT NULL COMMENT '剩余车位数',
  `start_time` time DEFAULT NULL COMMENT '开门时间',
  `end_time` time DEFAULT NULL COMMENT '关门时间',
  `type` int(11) DEFAULT NULL COMMENT '停车位类型\n1商业\n2路侧',
  `lon` double DEFAULT NULL COMMENT '经度',
  `lat` double DEFAULT NULL COMMENT '纬度',
  `address` varchar(45) DEFAULT NULL COMMENT '地址',
  `user_id` int(11) NOT NULL,
  `price` decimal(4,1) DEFAULT NULL COMMENT '价格',
  `img` varchar(45) DEFAULT NULL COMMENT '停车场图片',
  `name` varchar(100) DEFAULT NULL COMMENT '停车场名称',
  PRIMARY KEY (`id`,`user_id`),
  KEY `fk_px_park_px_user1_idx` (`user_id`),
  CONSTRAINT `fk_px_park_px_user1` FOREIGN KEY (`user_id`) REFERENCES `px_user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `px_park` */

insert  into `px_park`(`id`,`total_num`,`remain_num`,`start_time`,`end_time`,`type`,`lon`,`lat`,`address`,`user_id`,`price`,`img`,`name`) values (1,20,5,'08:00:00','23:00:00',1,123.123456,123.123456,'不知道',1,'5.0',NULL,'上马停车场');

/*Table structure for table `px_parkrecord` */

DROP TABLE IF EXISTS `px_parkrecord`;

CREATE TABLE `px_parkrecord` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `park_id` int(11) DEFAULT NULL,
  `car_id` int(11) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `money` decimal(4,1) DEFAULT NULL,
  `privatePark_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_px_parkRecord_px_park1_idx` (`park_id`),
  KEY `fk_px_parkRecord_px_car1_idx` (`car_id`),
  KEY `fk_px_parkRecord_px_privatePark1_idx` (`privatePark_id`),
  CONSTRAINT `fk_px_parkRecord_px_car1` FOREIGN KEY (`car_id`) REFERENCES `px_car` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_px_parkRecord_px_park1` FOREIGN KEY (`park_id`) REFERENCES `px_park` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_px_parkRecord_px_privatePark1` FOREIGN KEY (`privatePark_id`) REFERENCES `px_privatepark` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `px_parkrecord` */

insert  into `px_parkrecord`(`id`,`park_id`,`car_id`,`start_time`,`end_time`,`money`,`privatePark_id`) values (3,1,1,'2015-10-26 10:00:00','2015-10-27 10:00:00','5.0',NULL);

/*Table structure for table `px_pricay` */

DROP TABLE IF EXISTS `px_pricay`;

CREATE TABLE `px_pricay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text COMMENT '条款文档',
  `time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `px_pricay` */

/*Table structure for table `px_privatepark` */

DROP TABLE IF EXISTS `px_privatepark`;

CREATE TABLE `px_privatepark` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lon` double DEFAULT NULL,
  `lat` double DEFAULT NULL,
  `address` varchar(45) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL COMMENT '开门时间',
  `end_time` datetime DEFAULT NULL COMMENT '关门时间',
  PRIMARY KEY (`id`,`user_id`),
  KEY `fk_px_privatePark_px_user1_idx` (`user_id`),
  CONSTRAINT `fk_px_privatePark_px_user1` FOREIGN KEY (`user_id`) REFERENCES `px_user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `px_privatepark` */

/*Table structure for table `px_rechargerecord` */

DROP TABLE IF EXISTS `px_rechargerecord`;

CREATE TABLE `px_rechargerecord` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) DEFAULT NULL COMMENT '充值手段\n1支付宝\n2微信\n3银行卡',
  `money` decimal(4,1) DEFAULT NULL COMMENT '充值金额',
  `time` datetime DEFAULT NULL COMMENT '充值时间',
  `user_id` int(11) DEFAULT NULL COMMENT '用户id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `px_rechargerecord` */

insert  into `px_rechargerecord`(`id`,`type`,`money`,`time`,`user_id`) values (1,1,'5.0','2015-10-26 10:00:00',1);

/*Table structure for table `px_rule` */

DROP TABLE IF EXISTS `px_rule`;

CREATE TABLE `px_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `park_id` int(11) NOT NULL,
  `ruleType_id` int(11) NOT NULL,
  PRIMARY KEY (`id`,`park_id`,`ruleType_id`),
  KEY `fk_px_rule_px_park1_idx` (`park_id`),
  KEY `fk_px_rule_px_rule_type1_idx` (`ruleType_id`),
  CONSTRAINT `fk_px_rule_px_park1` FOREIGN KEY (`park_id`) REFERENCES `px_park` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_px_rule_px_rule_type1` FOREIGN KEY (`ruleType_id`) REFERENCES `px_ruletype` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `px_rule` */

/*Table structure for table `px_ruletime` */

DROP TABLE IF EXISTS `px_ruletime`;

CREATE TABLE `px_ruletime` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `big_fee` decimal(4,1) DEFAULT NULL,
  `small_fee` decimal(4,1) DEFAULT NULL,
  `ruleType_id` int(11) NOT NULL,
  PRIMARY KEY (`id`,`ruleType_id`),
  KEY `fk_px_ruleTime_px_ruleType1_idx` (`ruleType_id`),
  CONSTRAINT `fk_px_ruleTime_px_ruleType1` FOREIGN KEY (`ruleType_id`) REFERENCES `px_ruletype` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `px_ruletime` */

/*Table structure for table `px_ruletype` */

DROP TABLE IF EXISTS `px_ruletype`;

CREATE TABLE `px_ruletype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL COMMENT '规则名称',
  `priority` int(11) DEFAULT NULL COMMENT '优先级\n123级依次递增',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `px_ruletype` */

/*Table structure for table `px_user` */

DROP TABLE IF EXISTS `px_user`;

CREATE TABLE `px_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phone` varchar(45) DEFAULT NULL COMMENT '手机号',
  `score` int(11) DEFAULT NULL COMMENT '积分',
  `nickname` varchar(45) DEFAULT NULL COMMENT '昵称',
  `name` varchar(45) DEFAULT NULL COMMENT '姓名',
  `member_id` int(11) DEFAULT NULL COMMENT '会员等级id',
  `my_money` varchar(45) DEFAULT NULL COMMENT '我的收入',
  `alipay` varchar(45) DEFAULT NULL COMMENT '支付宝',
  `wechartpay` varchar(45) DEFAULT NULL COMMENT '微信号',
  `band_card` varchar(45) DEFAULT NULL COMMENT '银行卡号',
  `pwd` varchar(45) DEFAULT NULL COMMENT '密码',
  `img` varchar(45) DEFAULT NULL COMMENT '头像图片',
  `remain` decimal(4,1) DEFAULT NULL COMMENT '余额',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `px_user` */

insert  into `px_user`(`id`,`phone`,`score`,`nickname`,`name`,`member_id`,`my_money`,`alipay`,`wechartpay`,`band_card`,`pwd`,`img`,`remain`) values (1,'123',NULL,'123',NULL,NULL,NULL,NULL,NULL,NULL,'289dff07669d7a23de0ef88d2f7129e7',NULL,NULL);

/*Table structure for table `px_user_car` */

DROP TABLE IF EXISTS `px_user_car`;

CREATE TABLE `px_user_car` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `car_id` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL COMMENT '数据是否有效（是否被删除）\n1有效\n2删除',
  PRIMARY KEY (`id`),
  KEY `fk_px_user_has_px_car_px_car1_idx` (`car_id`),
  KEY `fk_px_user_has_px_car_px_user_idx` (`user_id`),
  CONSTRAINT `fk_px_user_has_px_car_px_car1` FOREIGN KEY (`car_id`) REFERENCES `px_car` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_px_user_has_px_car_px_user` FOREIGN KEY (`user_id`) REFERENCES `px_user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `px_user_car` */

insert  into `px_user_car`(`id`,`user_id`,`car_id`,`status`) values (1,1,1,1);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
