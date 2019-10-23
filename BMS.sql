-- MySQL dump 10.13  Distrib 5.7.27, for Linux (x86_64)
--
-- Host: localhost    Database: BMS
-- ------------------------------------------------------
-- Server version	5.7.27

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
-- Table structure for table `BookInfos`
--

DROP TABLE IF EXISTS `BookInfos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BookInfos` (
  `CallNo` varchar(20) NOT NULL COMMENT '索书号',
  `Name` varchar(100) NOT NULL COMMENT '图书名称',
  `TypeNumber` varchar(10) NOT NULL COMMENT '类型Id',
  `Author` varchar(100) NOT NULL COMMENT '作者',
  `Translator` varchar(100) DEFAULT NULL COMMENT '译者',
  `Press` varchar(100) DEFAULT NULL COMMENT '出版社',
  `DatePublication` year(4) DEFAULT NULL COMMENT '出版日期',
  `Price` double DEFAULT NULL COMMENT '价格',
  `Page` int(11) DEFAULT NULL COMMENT '页码',
  PRIMARY KEY (`CallNo`),
  KEY `fk_BookInfos_1_idx` (`TypeNumber`),
  CONSTRAINT `fk_BookInfos_1` FOREIGN KEY (`TypeNumber`) REFERENCES `BookType` (`Number`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BookInfos`
--

LOCK TABLES `BookInfos` WRITE;
/*!40000 ALTER TABLE `BookInfos` DISABLE KEYS */;
INSERT INTO `BookInfos` VALUES ('A18/1 ','马克思箴言','A','袁芳',NULL,'吉林出版集团有限责任公司',2014,'33',193),('A223/0809','唯物主义和经验批判主义','A','列宁',NULL,'人民出版社',1960,'29',512),('A31/3','斯大林文选','A','斯大林',NULL,'人民出版社',1962,'22',663),('A44/4','毛泽东诗词集 ','A','毛泽东',NULL,'中央文献出版社 ',1996,'39',10265),('E072/1','指挥与控制战','E','宋跃进',NULL,'国防工业出版社 ',2012,'55',11207),('E08/1','军种的消亡','E','张啸天',NULL,'新华出版社',2013,'29.9',236),('TP312JA/17','实战JAVA高并发程序设计','T','葛一鸣，郭超',NULL,'电子工业出版社',2015,'49.9',12339);
/*!40000 ALTER TABLE `BookInfos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `BookStatistics`
--

DROP TABLE IF EXISTS `BookStatistics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BookStatistics` (
  `BookCallNo` varchar(20) NOT NULL COMMENT '索书号',
  `NumOfLoans` int(11) NOT NULL COMMENT '借出次数',
  `Surplus` int(11) NOT NULL COMMENT '剩余量',
  `Total` int(11) NOT NULL COMMENT '库存总量',
  PRIMARY KEY (`BookCallNo`),
  CONSTRAINT `FK_BookStatistics_CallNo` FOREIGN KEY (`BookCallNo`) REFERENCES `BookInfos` (`CallNo`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BookStatistics`
--

LOCK TABLES `BookStatistics` WRITE;
/*!40000 ALTER TABLE `BookStatistics` DISABLE KEYS */;
INSERT INTO `BookStatistics` VALUES ('A18/1 ',2,2,2),('A223/0809',0,2,2),('A31/3',0,1,1),('A44/4',0,2,2),('E072/1',0,3,3),('E08/1',1,2,3),('TP312JA/17',1,1,2);
/*!40000 ALTER TABLE `BookStatistics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `BookType`
--

DROP TABLE IF EXISTS `BookType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BookType` (
  `Number` varchar(45) NOT NULL COMMENT '编号',
  `Name` varchar(45) NOT NULL COMMENT '名称',
  PRIMARY KEY (`Number`),
  UNIQUE KEY `Number_UNIQUE` (`Number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BookType`
--

LOCK TABLES `BookType` WRITE;
/*!40000 ALTER TABLE `BookType` DISABLE KEYS */;
INSERT INTO `BookType` VALUES ('A','马克思主义、列宁主义、毛泽东思想、邓小平理论'),('B','哲学、宗教'),('C','社会科学总论'),('D','政治、法律'),('E','军事'),('F','经济'),('G','文化、科学、教育、体育'),('H','语言、文字'),('I','文学'),('J','艺术'),('K','历史、地理'),('N','自然科学总论'),('O','数理科学和化学'),('P','天文学、地球科学'),('Q','生物科学'),('R','医药、卫生'),('S','农业科学'),('T','工业技术'),('U','交通运输'),('V','航空、航天'),('X','环境科学、劳动保护科学（安全科学）'),('Z','综合性图书');
/*!40000 ALTER TABLE `BookType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Books`
--

DROP TABLE IF EXISTS `Books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Books` (
  `Barcode` varchar(40) NOT NULL COMMENT '条形码',
  `BookCallNo` varchar(20) NOT NULL COMMENT '索书号',
  `Operator` varchar(45) NOT NULL COMMENT '操作员',
  `State` bit(1) NOT NULL COMMENT '是否外借',
  `Disabled` bit(1) NOT NULL COMMENT '是否禁用',
  PRIMARY KEY (`Barcode`),
  KEY `fk_Books_1_idx` (`BookCallNo`),
  KEY `fk_Books_1_idx1` (`Operator`),
  CONSTRAINT `FK_Books_CallNo` FOREIGN KEY (`BookCallNo`) REFERENCES `BookInfos` (`CallNo`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_Books_Operator` FOREIGN KEY (`Operator`) REFERENCES `Operators` (`Number`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Books`
--

LOCK TABLES `Books` WRITE;
/*!40000 ALTER TABLE `Books` DISABLE KEYS */;
INSERT INTO `Books` VALUES ('A180001-01','A18/1 ','Admin',_binary '\0',_binary '\0'),('A180001-02','A18/1 ','Admin',_binary '\0',_binary '\0'),('A2230809-01','A223/0809','Admin',_binary '\0',_binary '\0'),('A2230809-02','A223/0809','Admin',_binary '\0',_binary '\0'),('A310003-01','A31/3','Admin',_binary '\0',_binary '\0'),('A440004-01','A44/4','Admin',_binary '\0',_binary '\0'),('A440004-02','A44/4','Admin',_binary '\0',_binary '\0'),('E0720001-01','E072/1','Admin',_binary '\0',_binary '\0'),('E0720001-02','E072/1','Admin',_binary '\0',_binary '\0'),('E0720001-03','E072/1','Admin',_binary '\0',_binary '\0'),('E080001-01','E08/1','Admin',_binary '',_binary '\0'),('E080001-02','E08/1','Admin',_binary '\0',_binary '\0'),('E080001-03','E08/1','Admin',_binary '\0',_binary '\0'),('TP312JA0017-01','TP312JA/17','Admin',_binary '',_binary '\0'),('TP312JA0017-02','TP312JA/17','Admin',_binary '\0',_binary '\0');
/*!40000 ALTER TABLE `Books` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Borrowing`
--

DROP TABLE IF EXISTS `Borrowing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Borrowing` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `BookBarcode` varchar(40) NOT NULL COMMENT '图书条形码',
  `RBarcode` varchar(40) NOT NULL COMMENT '读者条形码',
  `StartTime` datetime NOT NULL COMMENT '开始时间',
  `ExpiryTime` datetime NOT NULL COMMENT '到期时间',
  `DisabledRenew` bit(1) NOT NULL COMMENT '禁用续借',
  `Operator` varchar(20) NOT NULL COMMENT '操作员',
  PRIMARY KEY (`Id`),
  KEY `fk_Borrowing_1_idx` (`BookBarcode`),
  KEY `FK_Borrowing_RBarcode_idx` (`RBarcode`),
  KEY `FK_Borrowing_Operator_idx` (`Operator`),
  CONSTRAINT `FK_Borrowing_BookBarcode` FOREIGN KEY (`BookBarcode`) REFERENCES `Books` (`Barcode`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `FK_Borrowing_Operator` FOREIGN KEY (`Operator`) REFERENCES `Operators` (`Number`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `FK_Borrowing_RBarcode` FOREIGN KEY (`RBarcode`) REFERENCES `Readers` (`Barcode`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Borrowing`
--

LOCK TABLES `Borrowing` WRITE;
/*!40000 ALTER TABLE `Borrowing` DISABLE KEYS */;
INSERT INTO `Borrowing` VALUES (3,'E080001-01','T07001','2019-07-12 00:00:00','2019-09-12 00:00:00',_binary '\0','18001'),(4,'TP312JA0017-01','T08002','2019-07-15 00:00:00','2019-09-15 00:00:00',_binary '\0','18002');
/*!40000 ALTER TABLE `Borrowing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `BorrowingRecords`
--

DROP TABLE IF EXISTS `BorrowingRecords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BorrowingRecords` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `BookBarcode` varchar(40) NOT NULL COMMENT '图书条形码',
  `RBarcode` varchar(40) NOT NULL COMMENT '读者条形码',
  `StartTime` datetime NOT NULL COMMENT '开始时间',
  `ExpiryTime` datetime NOT NULL COMMENT '到期时间',
  `DisabledRenew` bit(1) NOT NULL COMMENT '禁用续借',
  `Operator` varchar(20) NOT NULL COMMENT '操作员',
  PRIMARY KEY (`Id`),
  KEY `F_BorrowKingRecords_BookBarcode_idx` (`BookBarcode`),
  KEY `FK_BorrowingRecords_RBarcode_idx` (`RBarcode`),
  KEY `FK_BorrowingRecords_Operator_idx` (`Operator`),
  CONSTRAINT `FK_BorrowingRecords_Operator` FOREIGN KEY (`Operator`) REFERENCES `Operators` (`Number`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `FK_BorrowingRecords_RBarcode` FOREIGN KEY (`RBarcode`) REFERENCES `Readers` (`Barcode`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `F_BorrowKingRecords_BookBarcode` FOREIGN KEY (`BookBarcode`) REFERENCES `Books` (`Barcode`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BorrowingRecords`
--

LOCK TABLES `BorrowingRecords` WRITE;
/*!40000 ALTER TABLE `BorrowingRecords` DISABLE KEYS */;
INSERT INTO `BorrowingRecords` VALUES (1,'A180001-01','H20164089101','2019-06-11 00:00:00','2019-07-11 00:00:00',_binary '\0','Admin'),(2,'A180001-02','H20164089101','2019-07-01 00:00:00','2019-07-31 00:00:00',_binary '\0','Admin');
/*!40000 ALTER TABLE `BorrowingRecords` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Fine`
--

DROP TABLE IF EXISTS `Fine`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Fine` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `BookBarcode` varchar(40) NOT NULL COMMENT '图书条形码',
  `RBarcode` varchar(40) NOT NULL COMMENT '读者条形码',
  `SBId` int(11) NOT NULL COMMENT '归还Id',
  `Price` double NOT NULL,
  `Payment` bit(1) NOT NULL,
  `Operator` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `fk_Fine_1_idx` (`BookBarcode`),
  KEY `fk_Fine_2_idx` (`RBarcode`),
  KEY `fk_Fine_3_idx` (`SBId`),
  KEY `fk_Fine_4_idx` (`Operator`),
  CONSTRAINT `fk_Fine_1` FOREIGN KEY (`BookBarcode`) REFERENCES `Books` (`Barcode`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_Fine_2` FOREIGN KEY (`RBarcode`) REFERENCES `Readers` (`Barcode`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_Fine_3` FOREIGN KEY (`SBId`) REFERENCES `SendBack` (`Id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_Fine_4` FOREIGN KEY (`Operator`) REFERENCES `Operators` (`Number`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Fine`
--

LOCK TABLES `Fine` WRITE;
/*!40000 ALTER TABLE `Fine` DISABLE KEYS */;
INSERT INTO `Fine` VALUES (1,'A180001-01','H20164089101',1,0.6,_binary '\0',NULL);
/*!40000 ALTER TABLE `Fine` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Operators`
--

DROP TABLE IF EXISTS `Operators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Operators` (
  `Number` varchar(20) NOT NULL COMMENT '编号',
  `Name` varchar(20) NOT NULL COMMENT '姓名',
  `PassWord` varchar(20) NOT NULL COMMENT '密码',
  PRIMARY KEY (`Number`),
  UNIQUE KEY `Number_UNIQUE` (`Number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Operators`
--

LOCK TABLES `Operators` WRITE;
/*!40000 ALTER TABLE `Operators` DISABLE KEYS */;
INSERT INTO `Operators` VALUES ('18001','田玲','12345'),('18002','王海','88888'),('Admin','Admin','Admin');
/*!40000 ALTER TABLE `Operators` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ReaderType`
--

DROP TABLE IF EXISTS `ReaderType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ReaderType` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(20) NOT NULL COMMENT '类型名称',
  `LoanPeriod` int(11) NOT NULL COMMENT '可借天数',
  `DebitAmount` double NOT NULL COMMENT '欠款额度',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Name_UNIQUE` (`Name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ReaderType`
--

LOCK TABLES `ReaderType` WRITE;
/*!40000 ALTER TABLE `ReaderType` DISABLE KEYS */;
INSERT INTO `ReaderType` VALUES (1,'老师',60,5),(2,'学生',30,1);
/*!40000 ALTER TABLE `ReaderType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Readers`
--

DROP TABLE IF EXISTS `Readers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Readers` (
  `Number` varchar(20) NOT NULL COMMENT '编号',
  `Name` varchar(20) NOT NULL COMMENT '姓名',
  `PassWord` varchar(20) NOT NULL COMMENT '密码',
  `TypeId` int(11) NOT NULL COMMENT '类型Id',
  `Barcode` varchar(40) NOT NULL COMMENT '条形码',
  `Sex` varchar(4) DEFAULT NULL COMMENT '性别',
  `EnrollmentYear` year(4) NOT NULL COMMENT '入学年份',
  `RegistrationDate` date DEFAULT NULL COMMENT '登记日期',
  `Loss` bit(1) NOT NULL COMMENT '是否挂失',
  `Disabled` bit(1) NOT NULL COMMENT '是否禁用',
  PRIMARY KEY (`Number`),
  UNIQUE KEY `Number_UNIQUE` (`Number`),
  UNIQUE KEY `Barcode_UNIQUE` (`Barcode`),
  KEY `FK_Readers_TypeId_idx` (`TypeId`),
  CONSTRAINT `FK_Readers_TypeId` FOREIGN KEY (`TypeId`) REFERENCES `ReaderType` (`Id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Readers`
--

LOCK TABLES `Readers` WRITE;
/*!40000 ALTER TABLE `Readers` DISABLE KEYS */;
INSERT INTO `Readers` VALUES ('07001','李老师','tsg123456',1,'T07001','女',2001,'2001-09-01',_binary '\0',_binary '\0'),('08001','王老师','tsg123456',1,'T08001','男',2001,'2001-09-01',_binary '\0',_binary '\0'),('08002','张老师','tsg123456',1,'T08002','男',2002,'2002-09-01',_binary '\0',_binary '\0'),('20164089101','张三','tsg123456',2,'H20164089101','男',2016,'2016-09-01',_binary '\0',_binary '\0'),('20164089102','李四','tsg123456',2,'H20164089102','女',2016,'2016-09-01',_binary '\0',_binary '\0');
/*!40000 ALTER TABLE `Readers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `SendBack`
--

DROP TABLE IF EXISTS `SendBack`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SendBack` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `BookBarcode` varchar(40) NOT NULL COMMENT '图书条形码',
  `RBarcode` varchar(40) NOT NULL COMMENT '读者条形码',
  `ReturnTime` datetime NOT NULL COMMENT '归还时间',
  `Operator` varchar(20) NOT NULL COMMENT '操作员',
  `BRId` int(11) NOT NULL COMMENT '借阅记录Id',
  PRIMARY KEY (`Id`),
  KEY `fk_SendBack_1_idx` (`BookBarcode`),
  KEY `fk_SendBack_2_idx` (`RBarcode`),
  KEY `fk_SendBack_3_idx` (`Operator`),
  KEY `fk_SendBack_1_idx1` (`BRId`),
  CONSTRAINT `FK_SendBack_BRId` FOREIGN KEY (`BRId`) REFERENCES `BorrowingRecords` (`Id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `FK_SendBack_BookBarcode` FOREIGN KEY (`BookBarcode`) REFERENCES `Books` (`Barcode`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `FK_SendBack_Operator` FOREIGN KEY (`Operator`) REFERENCES `Operators` (`Number`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `FK_SendBack_RBarcode` FOREIGN KEY (`RBarcode`) REFERENCES `Readers` (`Barcode`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `SendBack`
--

LOCK TABLES `SendBack` WRITE;
/*!40000 ALTER TABLE `SendBack` DISABLE KEYS */;
INSERT INTO `SendBack` VALUES (1,'A180001-01','H20164089101','2019-07-17 00:00:00','Admin',1),(2,'A180001-02','H20164089101','2019-07-28 00:00:00','Admin',2);
/*!40000 ALTER TABLE `SendBack` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-08-15 21:16:39
