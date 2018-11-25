/*
 Navicat Premium Data Transfer

 Source Server         : 新加坡
 Source Server Type    : MySQL
 Source Server Version : 50723
 Source Host           : 45.76.145.73:3306
 Source Schema         : snow

 Target Server Type    : MySQL
 Target Server Version : 50723
 File Encoding         : 65001

 Date: 20/11/2018 22:09:19
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ssc_address
-- ----------------------------
DROP TABLE IF EXISTS `ssc_address`;
CREATE TABLE `ssc_address`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '收货人姓名',
  `phone` bigint(20) NOT NULL COMMENT '收货人手机号',
  `details` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '详细地址',
  `postal_code` int(10) NOT NULL COMMENT '邮政编码',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '收货地址表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ssc_alipay
-- ----------------------------
DROP TABLE IF EXISTS `ssc_alipay`;
CREATE TABLE `ssc_alipay`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `alipay_account` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '支付宝帐号',
  `alipay_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '支付宝姓名',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ssc_apply
-- ----------------------------
DROP TABLE IF EXISTS `ssc_apply`;
CREATE TABLE `ssc_apply`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL DEFAULT 0 COMMENT '订单表id',
  `user_id` int(11) NOT NULL DEFAULT 0 COMMENT '用户id',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0-失败，1-成功，2-待处理',
  `purpose` tinyint(1) NOT NULL COMMENT '申请目的，1-退货，2-提货，3-金币兑换',
  `gold` int(11) NOT NULL DEFAULT 0 COMMENT '兑换金币',
  `created_date` int(11) NOT NULL COMMENT '创建时间',
  `updated_date` int(11) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX ```user_id```(`user_id`) USING BTREE,
  INDEX ```purpose```(`purpose`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '申请表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ssc_award_info
-- ----------------------------
DROP TABLE IF EXISTS `ssc_award_info`;
CREATE TABLE `ssc_award_info`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `term_num` varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '哪一期,期号',
  `result` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '开奖结果',
  `created_date` int(11) NOT NULL,
  `updated_date` int(11) NOT NULL,
  `win` tinyint(1) NULL DEFAULT NULL COMMENT '获胜，0-瑞雪，1-丰年',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `term_num`(`term_num`) USING BTREE,
  INDEX `result`(`result`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 477 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '开奖信息表' ROW_FORMAT = Dynamic;


-- ----------------------------
-- Table structure for ssc_extract
-- ----------------------------
DROP TABLE IF EXISTS `ssc_extract`;
CREATE TABLE `ssc_extract`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `amount` int(10) NOT NULL COMMENT '提现金额',
  `real_amount` int(11) NOT NULL COMMENT '实际到账金额',
  `way` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `alipay_id` int(11) NOT NULL COMMENT '绑定支付宝id',
  `status` int(1) NOT NULL COMMENT '提现状态，0-失败，1-成功,2-待处理',
  `created_date` int(11) NOT NULL COMMENT '提现时间',
  `updated_date` int(11) NULL DEFAULT NULL,
  `refuse_reason` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '被拒绝理由',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户提现表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ssc_goods
-- ----------------------------
DROP TABLE IF EXISTS `ssc_goods`;
CREATE TABLE `ssc_goods`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商品名称',
  `price` int(10) NOT NULL COMMENT '商品价格',
  `success_price` int(10) NOT NULL COMMENT '升级成功后价格',
  `pic_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '图片地址',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ssc_goods
-- ----------------------------
INSERT INTO `ssc_goods` VALUES (1, '上品柠檬红茶', 88, 118, '\\static\\goods\\goods_1.png');
INSERT INTO `ssc_goods` VALUES (2, '桐木关金骏眉', 128, 179, '\\static\\goods\\goods_2.png');
INSERT INTO `ssc_goods` VALUES (3, '清香手工铁观音', 188, 272, '\\static\\goods\\goods_3.png');
INSERT INTO `ssc_goods` VALUES (4, '特级烟熏正山小种', 268, 393, '\\static\\goods\\goods_4.png');

-- ----------------------------
-- Table structure for ssc_group_income
-- ----------------------------
DROP TABLE IF EXISTS `ssc_group_income`;
CREATE TABLE `ssc_group_income`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL COMMENT '用户id',
  `income` float NOT NULL COMMENT '今日团队收入',
  `bonus` float NOT NULL COMMENT '用户分红,团队收入乘分成比例',
  `out` float NOT NULL COMMENT '今日支出',
  `created_date` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX ```created_date```(`created_date`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '团队表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ssc_gussing
-- ----------------------------
DROP TABLE IF EXISTS `ssc_gussing`;
CREATE TABLE `ssc_gussing`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `goods_id` int(3) NOT NULL COMMENT '选择商品的id',
  `goods_sum` int(10) NOT NULL,
  `status` bit(1) NOT NULL COMMENT '0-失败，1-成功',
  `created_date` int(11) NOT NULL COMMENT '下单时间',
  `updated_date` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '下注记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ssc_order
-- ----------------------------
DROP TABLE IF EXISTS `ssc_order`;
CREATE TABLE `ssc_order`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  `goods_num` int(5) NOT NULL COMMENT '购买商品数量',
  `amount` int(10) NOT NULL DEFAULT 0 COMMENT '总价格',
  `address_id` int(11) NOT NULL DEFAULT 0 COMMENT '收货地址id',
  `created_date` int(11) NOT NULL COMMENT '创建时间',
  `guessing` tinyint(1) NOT NULL COMMENT '升级选择，1-丰年，0-瑞雪',
  `award_id` int(11) NOT NULL COMMENT '竞猜期数，award_info表的id',
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '状态，1-退货中，2-提货中，3-已提货，4-已退货，5-已转为金币',
  `refuse_reason` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '被拒绝理由',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `award_id`(`award_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ssc_recharge
-- ----------------------------
DROP TABLE IF EXISTS `ssc_recharge`;
CREATE TABLE `ssc_recharge`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL COMMENT '用户id',
  `amount` int(10) NOT NULL COMMENT '充值金额',
  `way` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '充值方式',
  `status` tinyint(1) NOT NULL COMMENT '充值状态，2- 待处理，0-失败，1-成功',
  `created_date` int(11) NOT NULL,
  `updated_date` int(11) NULL DEFAULT NULL,
  `refuse_reason` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '被拒绝理由',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户充值表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ssc_recharge
-- ----------------------------
INSERT INTO `ssc_recharge` VALUES (5, 9, 10, '支付宝', 2, 1542205305, NULL, NULL);
INSERT INTO `ssc_recharge` VALUES (6, 9, 10, '微信', 2, 1542205321, NULL, NULL);
INSERT INTO `ssc_recharge` VALUES (7, 8, 10, '微信', 2, 1542630438, NULL, NULL);

-- ----------------------------
-- Table structure for ssc_user_role
-- ----------------------------
DROP TABLE IF EXISTS `ssc_user_role`;
CREATE TABLE `ssc_user_role`  (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `proportion` float NOT NULL COMMENT '分红比例',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户角色表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ssc_users
-- ----------------------------
DROP TABLE IF EXISTS `ssc_users`;
CREATE TABLE `ssc_users`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户名',
  `passwd` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '密码',
  `role` tinyint(10) NOT NULL DEFAULT 0 COMMENT '角色，关联user_role表',
  `parent_id` int(10) NOT NULL DEFAULT 0 COMMENT '上级id',
  `money` int(10) NOT NULL DEFAULT 0 COMMENT '账户余额',
  `gold` int(10) NOT NULL DEFAULT 0 COMMENT '账户金币',
  `status` bit(1) NOT NULL DEFAULT b'1' COMMENT '账户状态，0-禁用，1-正常使用',
  `tel` bigint(15) NULL DEFAULT 0 COMMENT '电话号码',
  `created_date` int(11) NOT NULL,
  `email` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `photo` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '头像url',
  `frozen_money` int(11) NULL DEFAULT 0 COMMENT '冻结金额',
  `frozen_gold` int(11) NULL DEFAULT 0 COMMENT '冻结金币',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `name`(`name`) USING BTREE,
  INDEX `parent_id`(`parent_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ssc_users
-- ----------------------------
INSERT INTO `ssc_users` VALUES (8, 'wangc', 'aeb012d0c9505be9d7cd9adef91eff14', 1, 0, 0, 0, b'1', 18868881888, 1542202990, '', '/uploads/photo_1.jpg', 0, 0);
INSERT INTO `ssc_users` VALUES (9, '1515927676', '11a27a75597141993543397dd0e8339f', 0, 0, 0, 0, b'1', 0, 1542205247, '', '/uploads/photo_1.jpg', 0, 0);

SET FOREIGN_KEY_CHECKS = 1;
