/*
 Navicat Premium Data Transfer

 Source Server         : MysqlConfig
 Source Server Type    : MySQL
 Source Server Version : 100411
 Source Host           : localhost:3306
 Source Schema         : db_bk_delivery

 Target Server Type    : MySQL
 Target Server Version : 100411
 File Encoding         : 65001

 Date: 06/08/2024 10:32:43
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for dlv_pickup
-- ----------------------------
DROP TABLE IF EXISTS `dlv_pickup`;
CREATE TABLE `dlv_pickup`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `pickup_date` date NOT NULL,
  `paket_status` enum('COD','SEMI LUNAS','FULL LUNAS') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'COD | SEMI LUNAS | FULL LUNAS',
  `kurir_id` int NOT NULL,
  `resi_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `cs_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `seller_phone_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `price` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `shiping_cost` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status_pickup` enum('PROSES','PENDING','SUKSES','CANCEL') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'PROSES | PENDING | SUKSES | CANCEL',
  `date_created` datetime NOT NULL,
  `date_modified` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of dlv_pickup
-- ----------------------------

-- ----------------------------
-- Table structure for mst_config
-- ----------------------------
DROP TABLE IF EXISTS `mst_config`;
CREATE TABLE `mst_config`  (
  `poin_reward` int NOT NULL
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of mst_config
-- ----------------------------
INSERT INTO `mst_config` VALUES (1);

-- ----------------------------
-- Table structure for mst_kurir
-- ----------------------------
DROP TABLE IF EXISTS `mst_kurir`;
CREATE TABLE `mst_kurir`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `kurir_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `birdth_place` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `birdth_date` date NOT NULL,
  `batam_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `phone_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Single | Menikah',
  `partner_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `phone_number_partner` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `phone_number_family` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `profile_pic` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `is_active` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '1 = ON | 0 = OFF',
  `is_validate` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '1 = Verified | 0 = Pending',
  `user_id` int NULL DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_modified` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of mst_kurir
-- ----------------------------
INSERT INTO `mst_kurir` VALUES (1, 'Administrator', '', '2024-08-06', '', '', '', NULL, NULL, NULL, '', '1', '1', 1, '2024-08-06 10:31:08', NULL);

-- ----------------------------
-- Table structure for mst_role_access
-- ----------------------------
DROP TABLE IF EXISTS `mst_role_access`;
CREATE TABLE `mst_role_access`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `role_access` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of mst_role_access
-- ----------------------------
INSERT INTO `mst_role_access` VALUES (1, 'Administrator', '2023-12-06 15:49:22');
INSERT INTO `mst_role_access` VALUES (2, 'Kurir', '2023-12-06 15:49:22');

-- ----------------------------
-- Table structure for mst_user
-- ----------------------------
DROP TABLE IF EXISTS `mst_user`;
CREATE TABLE `mst_user`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `full_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role_access_id` int NOT NULL,
  `date_created` datetime NOT NULL,
  `date_modified` datetime NULL DEFAULT NULL,
  `is_active` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_user_idroleakses`(`role_access_id`) USING BTREE,
  CONSTRAINT `mst_user_ibfk_1` FOREIGN KEY (`role_access_id`) REFERENCES `mst_role_access` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of mst_user
-- ----------------------------
INSERT INTO `mst_user` VALUES (1, 'administrator', 'administrator', 'Administrator', 1, '2023-12-06 16:08:35', '2024-07-05 11:29:46', '1');

-- ----------------------------
-- Table structure for trx_delivery
-- ----------------------------
DROP TABLE IF EXISTS `trx_delivery`;
CREATE TABLE `trx_delivery`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `kurir_id` int NOT NULL,
  `status_delivery` enum('PROSES','PENDING','SUKSES','CANCEL') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'PROSES | PENDING | SUKSES | CANCEL',
  `pickup_id` int NOT NULL,
  `delivery_date` date NOT NULL,
  `picture_deliv` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `picture_finish` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `delivery_finish_date` date NULL DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_modified` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of trx_delivery
-- ----------------------------

-- ----------------------------
-- Table structure for trx_reward
-- ----------------------------
DROP TABLE IF EXISTS `trx_reward`;
CREATE TABLE `trx_reward`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `seller_phone_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status_claim` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Claim | Pending',
  `counting` int NOT NULL,
  `milestone_date` date NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of trx_reward
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
