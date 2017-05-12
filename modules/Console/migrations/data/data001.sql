/*
Navicat MySQL Data Transfer

Source Server         : local_db
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : temp

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-01-11 17:44:22
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `c2_entity_attachments`
-- ----------------------------
DROP TABLE IF EXISTS `c2_entity_attachments`;
CREATE TABLE `c2_entity_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entity_id` int(11) DEFAULT '0',
  `entity_class` varchar(255) DEFAULT NULL,
  `type` int(11) DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `hash` varchar(255) DEFAULT NULL,
  `extension` varchar(255) DEFAULT NULL,
  `size` int(11) DEFAULT '0',
  `mime_type` varchar(255) DEFAULT NULL,
  `logic_path` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `position` int(11) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Index_1` (`entity_id`,`type`),
  KEY `Index_2` (`entity_class`,`type`),
  KEY `Index_3` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of c2_entity_attachments
-- ----------------------------
