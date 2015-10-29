-- MySQL dump 10.13  Distrib 5.6.24, for osx10.8 (x86_64)
--
-- Host: localhost    Database: parkdb
-- ------------------------------------------------------
-- Server version	5.5.42

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table ` px_evaluate`
--

DROP TABLE IF EXISTS ` px_evaluate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  CONSTRAINT `fk_ evaluate_px_parkRecord1` FOREIGN KEY (`parkRecord_id`) REFERENCES `px_parkRecord` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ evaluate_px_user1` FOREIGN KEY (`user_id`) REFERENCES `px_user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table ` px_evaluate`
--

LOCK TABLES ` px_evaluate` WRITE;
/*!40000 ALTER TABLE ` px_evaluate` DISABLE KEYS */;
/*!40000 ALTER TABLE ` px_evaluate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `px_appointment`
--

DROP TABLE IF EXISTS `px_appointment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `px_appointment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `privatePark_id` int(11) NOT NULL,
  `start_time` datetime DEFAULT NULL COMMENT '开始时间',
  `end_time` datetime DEFAULT NULL COMMENT '结束时间',
  PRIMARY KEY (`id`,`user_id`,`privatePark_id`),
  KEY `fk_px_appointment_px_user1_idx` (`user_id`),
  KEY `fk_px_appointment_px_privatePark1_idx` (`privatePark_id`),
  CONSTRAINT `fk_px_appointment_px_privatePark1` FOREIGN KEY (`privatePark_id`) REFERENCES `px_privatePark` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_px_appointment_px_user1` FOREIGN KEY (`user_id`) REFERENCES `px_user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `px_appointment`
--

LOCK TABLES `px_appointment` WRITE;
/*!40000 ALTER TABLE `px_appointment` DISABLE KEYS */;
/*!40000 ALTER TABLE `px_appointment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `px_captcha`
--

DROP TABLE IF EXISTS `px_captcha`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `px_captcha` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `captcha` varchar(45) DEFAULT NULL COMMENT '验证码',
  `user_id` int(11) DEFAULT NULL,
  `time` datetime DEFAULT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `px_captcha`
--

LOCK TABLES `px_captcha` WRITE;
/*!40000 ALTER TABLE `px_captcha` DISABLE KEYS */;
/*!40000 ALTER TABLE `px_captcha` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `px_car`
--

DROP TABLE IF EXISTS `px_car`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `px_car` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no` varchar(45) DEFAULT NULL COMMENT '车牌号',
  `type` varchar(45) DEFAULT NULL COMMENT '类型\n1小车\n2大车\n',
  `color` varchar(45) DEFAULT NULL COMMENT '颜色',
  `brand_id` int(11) DEFAULT NULL COMMENT '品牌id',
  `model_id` int(11) DEFAULT NULL COMMENT '类型id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `px_car`
--

LOCK TABLES `px_car` WRITE;
/*!40000 ALTER TABLE `px_car` DISABLE KEYS */;
INSERT INTO `px_car` VALUES (1,'鲁B123','1',NULL,NULL,NULL);
/*!40000 ALTER TABLE `px_car` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `px_collect`
--

DROP TABLE IF EXISTS `px_collect`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  CONSTRAINT `fk_px_collect_px_privatePark1` FOREIGN KEY (`privatePark_id`) REFERENCES `px_privatePark` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_px_collect_px_user1` FOREIGN KEY (`user_id`) REFERENCES `px_user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `px_collect`
--

LOCK TABLES `px_collect` WRITE;
/*!40000 ALTER TABLE `px_collect` DISABLE KEYS */;
/*!40000 ALTER TABLE `px_collect` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `px_park`
--

DROP TABLE IF EXISTS `px_park`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  PRIMARY KEY (`id`,`user_id`),
  KEY `fk_px_park_px_user1_idx` (`user_id`),
  CONSTRAINT `fk_px_park_px_user1` FOREIGN KEY (`user_id`) REFERENCES `px_user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `px_park`
--

LOCK TABLES `px_park` WRITE;
/*!40000 ALTER TABLE `px_park` DISABLE KEYS */;
INSERT INTO `px_park` VALUES (1,20,5,'08:00:00','23:00:00',1,123.123456,123.123456,'不知道',1,5.0,NULL);
/*!40000 ALTER TABLE `px_park` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `px_parkRecord`
--

DROP TABLE IF EXISTS `px_parkRecord`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `px_parkRecord` (
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
  CONSTRAINT `fk_px_parkRecord_px_privatePark1` FOREIGN KEY (`privatePark_id`) REFERENCES `px_privatePark` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `px_parkRecord`
--

LOCK TABLES `px_parkRecord` WRITE;
/*!40000 ALTER TABLE `px_parkRecord` DISABLE KEYS */;
INSERT INTO `px_parkRecord` VALUES (3,1,1,'2015-10-26 10:00:00','2015-10-27 10:00:00',5.0,NULL);
/*!40000 ALTER TABLE `px_parkRecord` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `px_pricay`
--

DROP TABLE IF EXISTS `px_pricay`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `px_pricay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text COMMENT '条款文档',
  `time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `px_pricay`
--

LOCK TABLES `px_pricay` WRITE;
/*!40000 ALTER TABLE `px_pricay` DISABLE KEYS */;
/*!40000 ALTER TABLE `px_pricay` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `px_privatePark`
--

DROP TABLE IF EXISTS `px_privatePark`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `px_privatePark` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `px_privatePark`
--

LOCK TABLES `px_privatePark` WRITE;
/*!40000 ALTER TABLE `px_privatePark` DISABLE KEYS */;
/*!40000 ALTER TABLE `px_privatePark` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `px_rechargeRecord`
--

DROP TABLE IF EXISTS `px_rechargeRecord`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `px_rechargeRecord` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) DEFAULT NULL COMMENT '充值手段\n1支付宝\n2微信\n3银行卡',
  `money` decimal(4,1) DEFAULT NULL COMMENT '充值金额',
  `time` datetime DEFAULT NULL COMMENT '充值时间',
  `user_id` int(11) DEFAULT NULL COMMENT '用户id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `px_rechargeRecord`
--

LOCK TABLES `px_rechargeRecord` WRITE;
/*!40000 ALTER TABLE `px_rechargeRecord` DISABLE KEYS */;
INSERT INTO `px_rechargeRecord` VALUES (1,1,5.0,'2015-10-26 10:00:00',1);
/*!40000 ALTER TABLE `px_rechargeRecord` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `px_rule`
--

DROP TABLE IF EXISTS `px_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `px_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `park_id` int(11) NOT NULL,
  `ruleType_id` int(11) NOT NULL,
  PRIMARY KEY (`id`,`park_id`,`ruleType_id`),
  KEY `fk_px_rule_px_park1_idx` (`park_id`),
  KEY `fk_px_rule_px_rule_type1_idx` (`ruleType_id`),
  CONSTRAINT `fk_px_rule_px_park1` FOREIGN KEY (`park_id`) REFERENCES `px_park` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_px_rule_px_rule_type1` FOREIGN KEY (`ruleType_id`) REFERENCES `px_ruleType` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `px_rule`
--

LOCK TABLES `px_rule` WRITE;
/*!40000 ALTER TABLE `px_rule` DISABLE KEYS */;
/*!40000 ALTER TABLE `px_rule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `px_ruleTime`
--

DROP TABLE IF EXISTS `px_ruleTime`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `px_ruleTime` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `big_fee` decimal(4,1) DEFAULT NULL,
  `small_fee` decimal(4,1) DEFAULT NULL,
  `ruleType_id` int(11) NOT NULL,
  PRIMARY KEY (`id`,`ruleType_id`),
  KEY `fk_px_ruleTime_px_ruleType1_idx` (`ruleType_id`),
  CONSTRAINT `fk_px_ruleTime_px_ruleType1` FOREIGN KEY (`ruleType_id`) REFERENCES `px_ruleType` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `px_ruleTime`
--

LOCK TABLES `px_ruleTime` WRITE;
/*!40000 ALTER TABLE `px_ruleTime` DISABLE KEYS */;
/*!40000 ALTER TABLE `px_ruleTime` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `px_ruleType`
--

DROP TABLE IF EXISTS `px_ruleType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `px_ruleType` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL COMMENT '规则名称',
  `priority` int(11) DEFAULT NULL COMMENT '优先级\n123级依次递增',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `px_ruleType`
--

LOCK TABLES `px_ruleType` WRITE;
/*!40000 ALTER TABLE `px_ruleType` DISABLE KEYS */;
/*!40000 ALTER TABLE `px_ruleType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `px_user`
--

DROP TABLE IF EXISTS `px_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `px_user`
--

LOCK TABLES `px_user` WRITE;
/*!40000 ALTER TABLE `px_user` DISABLE KEYS */;
INSERT INTO `px_user` VALUES (1,'123',NULL,'123',NULL,NULL,NULL,NULL,NULL,NULL,'289dff07669d7a23de0ef88d2f7129e7',NULL,NULL);
/*!40000 ALTER TABLE `px_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `px_user_car`
--

DROP TABLE IF EXISTS `px_user_car`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `px_user_car`
--

LOCK TABLES `px_user_car` WRITE;
/*!40000 ALTER TABLE `px_user_car` DISABLE KEYS */;
INSERT INTO `px_user_car` VALUES (1,1,1,2);
/*!40000 ALTER TABLE `px_user_car` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-10-28 22:11:10
