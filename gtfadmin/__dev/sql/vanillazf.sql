/*
Navicat MySQL Data Transfer

Source Server         : 1 - Local-Ubuntu
Source Server Version : 50535
Source Host           : localhost:3306
Source Database       : vanillazf

Target Server Type    : MYSQL
Target Server Version : 50535
File Encoding         : 65001

Date: 2014-05-06 13:01:50
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `user`
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(32) NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('6', 'mircea.baicu@lateral-inc.com', 'ea2e346423cdb1beae1b31f0cb2d1cea', 'Mircea Baicu', '2', '1');

-- ----------------------------
-- Table structure for `user_role`
-- ----------------------------
DROP TABLE IF EXISTS `user_role`;
CREATE TABLE `user_role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `parent` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of user_role
-- ----------------------------
INSERT INTO `user_role` VALUES ('1', 'Guest', null);
INSERT INTO `user_role` VALUES ('2', 'Admin', '1');

-- ----------------------------
-- Table structure for `user_role_permission`
-- ----------------------------
DROP TABLE IF EXISTS `user_role_permission`;
CREATE TABLE `user_role_permission` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role` int(11) unsigned NOT NULL,
  `resource` varchar(255) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `type` enum('allow','deny') NOT NULL DEFAULT 'deny',
  `weight` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=53 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of user_role_permission
-- ----------------------------
INSERT INTO `user_role_permission` VALUES ('1', '1', null, null, 'deny', '1');
INSERT INTO `user_role_permission` VALUES ('2', '1', 'fallback', null, 'allow', '2');
INSERT INTO `user_role_permission` VALUES ('7', '1', 'index', null, 'allow', '7');
INSERT INTO `user_role_permission` VALUES ('3', '1', 'login', null, 'allow', '3');
INSERT INTO `user_role_permission` VALUES ('4', '2', 'login', null, 'deny', '4');
INSERT INTO `user_role_permission` VALUES ('5', '1', 'logout', null, 'deny', '5');
INSERT INTO `user_role_permission` VALUES ('6', '2', 'logout', null, 'allow', '6');
INSERT INTO `user_role_permission` VALUES ('8', '2', 'admin', null, 'allow', '8');
