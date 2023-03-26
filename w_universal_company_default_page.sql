/*
 Navicat Premium Data Transfer

 Source Server         : 谢-云tenancy
 Source Server Type    : MySQL
 Source Server Version : 80018
 Source Host           : rm-bp1w1nmd4576u594cyo.mysql.rds.aliyuncs.com:3306
 Source Schema         : tenancy_xiesemi

 Target Server Type    : MySQL
 Target Server Version : 80018
 File Encoding         : 65001

 Date: 26/03/2023 10:06:21
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for w_universal_company_default_page
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_company_default_page`;
CREATE TABLE `w_universal_company_default_page`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `company_id` int(11) NULL DEFAULT NULL COMMENT '页面分组（菜单有用）',
  `cate` varchar(8) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '页面分类，wePub公众号；weApp小程序',
  `default_page_key` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '默认页面key',
  `sort` int(11) NULL DEFAULT 1000 COMMENT '排序',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `has_used` tinyint(1) NULL DEFAULT 0 COMMENT '有使用(0否,1是)',
  `is_lock` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未锁，1：已锁）',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未删，1：已删）',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '更新者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `company_id`(`company_id`, `cate`) USING BTREE,
  INDEX `cate`(`cate`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '页面表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_universal_group
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_group`;
CREATE TABLE `w_universal_group`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `group_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '页面分组',
  `main_style` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '系统主色风格',
  `need_manage` tinyint(1) NULL DEFAULT NULL COMMENT '管理员才能访问；w_system_company_user表',
  `need_login` tinyint(1) NULL DEFAULT NULL COMMENT '需要登录才能访问；',
  `fault_route` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '非管理员跳转路由',
  `fault_describe` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '不跳转时，页面上显示的简单文字提示',
  `sort` int(11) NULL DEFAULT 1000 COMMENT '排序',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `has_used` tinyint(1) NULL DEFAULT 0 COMMENT '有使用(0否,1是)',
  `is_lock` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未锁，1：已锁）',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未删，1：已删）',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '更新者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `update_time`(`update_time`) USING BTREE,
  INDEX `status`(`status`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '页面分组表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_universal_item
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_item`;
CREATE TABLE `w_universal_item`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `item_key` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '页面项key',
  `default_list_page` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '默认的展示\r\n页',
  `item_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '页面项名称',
  `in_adm` tinyint(1) NULL DEFAULT NULL COMMENT '后台有？',
  `in_web` tinyint(1) NULL DEFAULT NULL COMMENT '手机网页有？',
  `in_app` tinyint(1) NULL DEFAULT NULL COMMENT 'uniapp有？',
  `sort` int(11) NULL DEFAULT 1000 COMMENT '排序',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `has_used` tinyint(1) NULL DEFAULT 0 COMMENT '有使用(0否,1是)',
  `is_lock` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未锁，1：已锁）',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未删，1：已删）',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '更新者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `item_key`(`item_key`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '页面项' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_universal_item_banner
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_item_banner`;
CREATE TABLE `w_universal_item_banner`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `page_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '[冗]页面id',
  `page_item_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'page_item表的id',
  `banner_pic` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '[单图]轮播图',
  `class` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '轮播图样式(外框)',
  `sub_class` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '230217 <img 样式',
  `url` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '轮播跳转地址',
  `sort` int(11) NULL DEFAULT 1000 COMMENT '排序',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `has_used` tinyint(1) NULL DEFAULT 0 COMMENT '有使用(0否,1是)',
  `is_lock` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未锁，1：已锁）',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未删，1：已删）',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '更新者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `update_time`(`update_time`) USING BTREE,
  INDEX `status`(`status`) USING BTREE,
  INDEX `page_item_id`(`page_item_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '页面轮播项' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_universal_item_bao_order
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_item_bao_order`;
CREATE TABLE `w_universal_item_bao_order`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `page_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '[冗]页面id',
  `page_item_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'page_item表的id',
  `order_btn_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '下单按钮的id',
  `sort` int(11) NULL DEFAULT 1000 COMMENT '排序',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `has_used` tinyint(1) NULL DEFAULT 0 COMMENT '有使用(0否,1是)',
  `is_lock` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未锁，1：已锁）',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未删，1：已删）',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '更新者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `page_item_id`(`page_item_id`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `update_time`(`update_time`) USING BTREE,
  INDEX `status`(`status`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '包车下单列表配置' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_universal_item_btn
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_item_btn`;
CREATE TABLE `w_universal_item_btn`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `page_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '[冗]页面id',
  `page_item_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'page_item表的id',
  `subitem_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '子项id',
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '按钮名',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `show_condition` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `is_more` tinyint(1) NULL DEFAULT 0 COMMENT '是否隐藏于\"更多\"中',
  `cate` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '按钮分类：paginate:查询分页数据；save输入数据；get获取数据',
  `option` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '20230201选项',
  `size` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'mini' COMMENT 'ele用',
  `icon` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'ele用',
  `type` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'ele用',
  `pre_url` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '数据准备url（如获取导入字段）',
  `tpl_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '模板id；导入导出用',
  `data_url` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '数据Url',
  `param` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '传给数据Url的参数',
  `merge_table_sel` tinyint(1) NULL DEFAULT 0 COMMENT '参数是否合并表格选择项目？0否；1是',
  `trigger` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '后续触发：list:获取列表；data:获取数据',
  `after_url` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '数据提交完成后的跳转地址',
  `with_location` tinyint(1) NULL DEFAULT 0 COMMENT '是否上报定位数据',
  `title` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '标题',
  `confirm` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '操作确认',
  `class` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '表单标题',
  `layer_width` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `layer_height` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `clear_cache_key` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '清除前端本地缓存key',
  `form_check` tinyint(1) NULL DEFAULT 0 COMMENT '20220923:表单校验：0否；1是',
  `sort` int(11) NULL DEFAULT 1000 COMMENT '排序',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `has_used` tinyint(1) NULL DEFAULT 0 COMMENT '有使用(0否,1是)',
  `is_lock` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未锁，1：已锁）',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未删，1：已删）',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '更新者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `update_time`(`update_time`) USING BTREE,
  INDEX `status`(`status`) USING BTREE,
  INDEX `page_item_id`(`page_item_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '项目按钮' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_universal_item_card
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_item_card`;
CREATE TABLE `w_universal_item_card`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `page_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `page_item_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'page_item表的id',
  `title` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '卡片标题',
  `card_page_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '选项卡内嵌页id',
  `class` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `span` int(11) NULL DEFAULT 24 COMMENT '宽度：',
  `param` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '参数转换',
  `f_xs` int(11) NULL DEFAULT NULL COMMENT '响应式布局',
  `f_sm` int(11) NULL DEFAULT NULL COMMENT '响应式布局',
  `f_md` int(11) NULL DEFAULT NULL COMMENT '响应式布局',
  `f_lg` int(11) NULL DEFAULT NULL COMMENT '响应式布局',
  `f_xl` int(11) NULL DEFAULT NULL COMMENT '响应式布局',
  `sort` int(11) NULL DEFAULT 1000 COMMENT '排序',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `has_used` tinyint(1) NULL DEFAULT 0 COMMENT '有使用(0否,1是)',
  `is_lock` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未锁，1：已锁）',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未删，1：已删）',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '更新者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `update_time`(`update_time`) USING BTREE,
  INDEX `status`(`status`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '卡片内嵌页面' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_universal_item_cell
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_item_cell`;
CREATE TABLE `w_universal_item_cell`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `page_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `page_item_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '页面id',
  `title` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '标题',
  `type` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'link' COMMENT '类型：link链接；cache清缓存；',
  `icon` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '字体图标',
  `icon_color` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '图标颜色',
  `url` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '跳转地址',
  `class` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '样式',
  `sort` int(11) NULL DEFAULT 1000 COMMENT '排序',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `has_used` tinyint(1) NULL DEFAULT 0 COMMENT '有使用(0否,1是)',
  `is_lock` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未锁，1：已锁）',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未删，1：已删）',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '更新者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `update_time`(`update_time`) USING BTREE,
  INDEX `status`(`status`) USING BTREE,
  INDEX `page_item_id`(`page_item_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = 'cell单元格，可用于做个人中心菜单，具备跳转' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_universal_item_circuit_bus
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_item_circuit_bus`;
CREATE TABLE `w_universal_item_circuit_bus`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `page_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '[冗]页面id',
  `page_item_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'page_item表的id',
  `passenger_page` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '选择乘客页面key',
  `after_page` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '下单完成跳转页面key',
  `on_class` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '上车按钮样式',
  `off_class` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '下车按钮样式',
  `reselect_class` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '重选按钮样式',
  `order_class` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '下单按钮样式',
  `sort` int(11) NULL DEFAULT 1000 COMMENT '排序',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `has_used` tinyint(1) NULL DEFAULT 0 COMMENT '有使用(0否,1是)',
  `is_lock` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未锁，1：已锁）',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未删，1：已删）',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '更新者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `page_item_id`(`page_item_id`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `update_time`(`update_time`) USING BTREE,
  INDEX `status`(`status`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '班线详情' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_universal_item_circuit_home
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_item_circuit_home`;
CREATE TABLE `w_universal_item_circuit_home`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `page_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '[冗]页面id',
  `page_item_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'page_item表的id',
  `passenger_page` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '选择乘客页面key',
  `after_page` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '下单完成跳转页面key',
  `on_class` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '上车按钮样式',
  `off_class` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '下车按钮样式',
  `reselect_class` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '重选按钮样式',
  `order_class` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '下单按钮样式',
  `title_class` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '标题样式',
  `selected_class` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '选中样式',
  `sort` int(11) NULL DEFAULT 1000 COMMENT '排序',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `has_used` tinyint(1) NULL DEFAULT 0 COMMENT '有使用(0否,1是)',
  `is_lock` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未锁，1：已锁）',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未删，1：已删）',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '更新者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `page_item_id`(`page_item_id`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `update_time`(`update_time`) USING BTREE,
  INDEX `status`(`status`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '班线详情' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_universal_item_data_bar
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_item_data_bar`;
CREATE TABLE `w_universal_item_data_bar`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `page_item_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'page_item表的id',
  `title` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '数据标题',
  `data_key` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '数据key',
  `url` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '跳转的详情页面链接',
  `param` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '20221030:参数',
  `title_class` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '标题样式',
  `data_class` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '数据样式',
  `sort` int(11) NULL DEFAULT 1000 COMMENT '排序',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `has_used` tinyint(1) NULL DEFAULT 0 COMMENT '有使用(0否,1是)',
  `is_lock` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未锁，1：已锁）',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未删，1：已删）',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '更新者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `update_time`(`update_time`) USING BTREE,
  INDEX `status`(`status`) USING BTREE,
  INDEX `page_item_id`(`page_item_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '数据条：用于显示积分，余额，优惠券及首页统计数据……' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_universal_item_describe
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_item_describe`;
CREATE TABLE `w_universal_item_describe`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `page_item_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'page_item表的id',
  `home_img` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '图标',
  `describe` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '描述',
  `class` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `url` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '跳转地址',
  `sort` int(11) NULL DEFAULT 1000 COMMENT '排序',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `has_used` tinyint(1) NULL DEFAULT 0 COMMENT '有使用(0否,1是)',
  `is_lock` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未锁，1：已锁）',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未删，1：已删）',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '更新者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `page_item_id`(`page_item_id`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `update_time`(`update_time`) USING BTREE,
  INDEX `status`(`status`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '信息描述展示' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_universal_item_detail
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_item_detail`;
CREATE TABLE `w_universal_item_detail`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `page_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '[冗]页面id',
  `page_item_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'page_item表的id',
  `subitem_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '子级项id（不一定有）',
  `label` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '表单标题',
  `icon` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '20220428图标',
  `show_label` tinyint(1) NULL DEFAULT 1 COMMENT '显示标题?(PC用)',
  `field` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '表单字段',
  `type` varchar(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '表单类型',
  `option` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '选项',
  `show_condition` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '显示条件',
  `class` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '样式',
  `span` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '6' COMMENT '宽度：24等分',
  `layer_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '表单后按钮弹窗',
  `sort` int(11) NULL DEFAULT 1000 COMMENT '排序',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `has_used` tinyint(1) NULL DEFAULT 0 COMMENT '有使用(0否,1是)',
  `is_lock` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未锁，1：已锁）',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未删，1：已删）',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '更新者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `update_time`(`update_time`) USING BTREE,
  INDEX `status`(`status`) USING BTREE,
  INDEX `page_item_id`(`page_item_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '详情' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_universal_item_echarts
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_item_echarts`;
CREATE TABLE `w_universal_item_echarts`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `page_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '[冗]页面id',
  `page_item_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'page_item表的id',
  `chart_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '20220625图表key',
  `title` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '图表标题',
  `height` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '高度',
  `class` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '样式',
  `sort` int(11) NULL DEFAULT 1000 COMMENT '排序',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `has_used` tinyint(1) NULL DEFAULT 0 COMMENT '有使用(0否,1是)',
  `is_lock` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未锁，1：已锁）',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未删，1：已删）',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '更新者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `update_time`(`update_time`) USING BTREE,
  INDEX `status`(`status`) USING BTREE,
  INDEX `page_item_id`(`page_item_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '图表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_universal_item_flex
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_item_flex`;
CREATE TABLE `w_universal_item_flex`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `pid` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '归属的父级布局',
  `page_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `page_item_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'page_item表的id',
  `flex_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '标题',
  `inner_page_key` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '选项卡内嵌页id/key',
  `class` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `sort` int(11) NULL DEFAULT 1000 COMMENT '排序',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `has_used` tinyint(1) NULL DEFAULT 0 COMMENT '有使用(0否,1是)',
  `is_lock` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未锁，1：已锁）',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未删，1：已删）',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '更新者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `update_time`(`update_time`) USING BTREE,
  INDEX `status`(`status`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '灵活布局选项' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_universal_item_form
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_item_form`;
CREATE TABLE `w_universal_item_form`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `page_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '[冗]页面id',
  `page_item_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'page_item表的id',
  `subitem_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '子级项id（不一定有）',
  `label` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '表单标题',
  `show_label` tinyint(1) NULL DEFAULT 1 COMMENT '显示标题?(PC用)',
  `field` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '表单字段',
  `forcus_title` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '20220928 鼠标上移时，显示的字段内容',
  `default_val` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '默认值',
  `type` varchar(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '表单类型',
  `disabled` tinyint(1) NULL DEFAULT 0 COMMENT '禁用？0否；1是；禁用可见不可修改',
  `multi` tinyint(1) NULL DEFAULT NULL COMMENT '可输入多值?0否；1是；用于select',
  `is_must` tinyint(1) NULL DEFAULT 0 COMMENT '是否必填? 0否；1是',
  `option` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '选项',
  `update_url` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '数据更新链接',
  `show_condition` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '显示条件',
  `class` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '样式',
  `label_class` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '20230305:label标签样式',
  `base_class` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '20230221:form表单外框样式（标签+表单的外层）',
  `item_class` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '20230222:输入框，选择框样式',
  `span` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '6' COMMENT '宽度：24等分',
  `layer_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '表单后按钮弹窗',
  `sort` int(11) NULL DEFAULT 1000 COMMENT '排序',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `has_used` tinyint(1) NULL DEFAULT 0 COMMENT '有使用(0否,1是)',
  `is_lock` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未锁，1：已锁）',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未删，1：已删）',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '更新者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `update_time`(`update_time`) USING BTREE,
  INDEX `status`(`status`) USING BTREE,
  INDEX `page_item_id`(`page_item_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '表单' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_universal_item_form_rule
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_item_form_rule`;
CREATE TABLE `w_universal_item_form_rule`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `page_item_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '[冗]页面表单id',
  `key_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '字段名',
  `required` tinyint(1) NULL DEFAULT 0 COMMENT '必填(0否,1是)',
  `min` int(11) NULL DEFAULT NULL COMMENT '最小值',
  `max` int(11) NULL DEFAULT NULL COMMENT '最大值',
  `message` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '弹窗消息',
  `trigger` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '触发',
  `sort` int(11) NULL DEFAULT 1000 COMMENT '排序',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `has_used` tinyint(1) NULL DEFAULT 0 COMMENT '有使用(0否,1是)',
  `is_lock` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未锁，1：已锁）',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未删，1：已删）',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '更新者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `update_time`(`update_time`) USING BTREE,
  INDEX `status`(`status`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '表单验证规则' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_universal_item_ftable
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_item_ftable`;
CREATE TABLE `w_universal_item_ftable`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `page_item_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'page_item表的id',
  `label` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '' COMMENT '字段名',
  `place` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '' COMMENT 'head:表头；line:单独行',
  `class` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '表头样式',
  `td_class` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '内容样式',
  `width` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `show_condition` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '20220906:显示条件',
  `sort` int(11) NULL DEFAULT 1000 COMMENT '排序',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `has_used` tinyint(1) NULL DEFAULT 0 COMMENT '有使用(0否,1是)',
  `is_lock` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未锁，1：已锁）',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未删，1：已删）',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '更新者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `page_item_id`(`page_item_id`) USING BTREE,
  INDEX `label`(`label`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `update_time`(`update_time`) USING BTREE,
  INDEX `status`(`status`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '表格形式的表单输入框' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_universal_item_ftable_subitem
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_item_ftable_subitem`;
CREATE TABLE `w_universal_item_ftable_subitem`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ftable_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'page_item表的id',
  `item_key` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '项目key',
  `data_url` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '数据来源接口地址；type为list，form时使用,\r\nlist为分页数据，form为表单数据',
  `param` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '传递参数',
  `show_condition` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '显示条件',
  `value` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '值；\r\n当为grid宫格时，表示每行格数\r\n当为list时，表示查询id的字段(TODO优化)\r\n当为form时，有值表示无id也从接口取数据\r\n当为tab时，表示绑定dataData的key\r\n当为menu时，表示当前页面的pageKey',
  `class` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '样式',
  `title` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '标题：可选',
  `title_class` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '标题样式',
  `sort` int(11) NULL DEFAULT 1000 COMMENT '排序',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `has_used` tinyint(1) NULL DEFAULT 0 COMMENT '有使用(0否,1是)',
  `is_lock` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未锁，1：已锁）',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未删，1：已删）',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '更新者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `ftable_id`(`ftable_id`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `update_time`(`update_time`) USING BTREE,
  INDEX `status`(`status`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '表单项下挂子项目' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_universal_item_grid
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_item_grid`;
CREATE TABLE `w_universal_item_grid`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `page_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '[冗]页面id',
  `page_item_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'page_item表的id',
  `title` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '宫格标题',
  `icon_pic` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '[顺1]图标',
  `grid_icon` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '[顺2]宫格图标',
  `icon_color` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '[顺2]图标颜色',
  `jump_type` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '跳转类型',
  `url` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '宫格跳转地址',
  `appid` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '2023-01-01：跳转微信小程序的appid',
  `span` tinyint(1) NULL DEFAULT NULL COMMENT '20220616后台',
  `class` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '样式',
  `sub_class` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '子样式（小程序）',
  `icon_class` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '20230218图标样式',
  `title_class` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '20220516标题样式',
  `sort` int(11) NULL DEFAULT 1000 COMMENT '排序',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `has_used` tinyint(1) NULL DEFAULT 0 COMMENT '有使用(0否,1是)',
  `is_lock` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未锁，1：已锁）',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未删，1：已删）',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '更新者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `update_time`(`update_time`) USING BTREE,
  INDEX `status`(`status`) USING BTREE,
  INDEX `page_item_id`(`page_item_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '宫格' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_universal_item_iframe
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_item_iframe`;
CREATE TABLE `w_universal_item_iframe`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `page_item_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'page_item表的id',
  `url` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'url样式',
  `param` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '参数',
  `width` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '宽度',
  `height` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '高度',
  `sort` int(11) NULL DEFAULT 1000 COMMENT '排序',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `has_used` tinyint(1) NULL DEFAULT 0 COMMENT '有使用(0否,1是)',
  `is_lock` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未锁，1：已锁）',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未删，1：已删）',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '更新者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `page_item_id`(`page_item_id`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `update_time`(`update_time`) USING BTREE,
  INDEX `status`(`status`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '嵌套页配置' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_universal_item_list
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_item_list`;
CREATE TABLE `w_universal_item_list`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `page_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '[冗]页面id',
  `page_item_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'page_item表的id',
  `label` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '【增】标签',
  `field` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '【增】字段',
  `place` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '【增】位置',
  `item_style` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '项目样式：normal:常规；order:订单卡；brief简图卡(拼车首页)',
  `need_user` tinyint(1) NULL DEFAULT NULL COMMENT '20230319：是否需要用户信息（需要登录/授权手机号码）',
  `title_field` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '标题字段',
  `describe_field` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '描述字段',
  `tag_field` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '标签字段',
  `value_field` varchar(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '值字段',
  `main_pic_field` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '主图字段',
  `switch_field` varchar(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '开关字段',
  `ipage_field` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '嵌套页字段，为了支持不同类型（一般PC用）',
  `iquery_field` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '嵌套页传参请求字段（后端拼给前端）',
  `class` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `sub_class` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '0908:子级的样式',
  `status_field` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '0908:状态字段',
  `url` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '列表跳转地址',
  `update_url` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '数据更新接口地址',
  `update_confirm` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '0908:操作确认',
  `sort` int(11) NULL DEFAULT 1000 COMMENT '排序',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `has_used` tinyint(1) NULL DEFAULT 0 COMMENT '有使用(0否,1是)',
  `is_lock` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未锁，1：已锁）',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未删，1：已删）',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '更新者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `page_item_id`(`page_item_id`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `update_time`(`update_time`) USING BTREE,
  INDEX `status`(`status`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '页面列表项（1v1）' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_universal_item_menu
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_item_menu`;
CREATE TABLE `w_universal_item_menu`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `group_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '页面组id',
  `title` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '宫格标题',
  `page_key` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '页面key',
  `icon_pic` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '[顺1]图标',
  `grid_icon` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '[顺2]宫格图标',
  `icon_color` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '[顺2]图标颜色',
  `url` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '宫格跳转地址',
  `class` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '样式',
  `sort` int(11) NULL DEFAULT 1000 COMMENT '排序',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `has_used` tinyint(1) NULL DEFAULT 0 COMMENT '有使用(0否,1是)',
  `is_lock` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未锁，1：已锁）',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未删，1：已删）',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '更新者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `update_time`(`update_time`) USING BTREE,
  INDEX `status`(`status`) USING BTREE,
  INDEX `group_id`(`group_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '菜单-与宫格类似，但同分组内具备复用能力' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_universal_item_nav_bar
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_item_nav_bar`;
CREATE TABLE `w_universal_item_nav_bar`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `page_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '[冗]页面id',
  `page_item_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'page_item表的id',
  `title` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'title标题',
  `sort` int(11) NULL DEFAULT 1000 COMMENT '排序',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `has_used` tinyint(1) NULL DEFAULT 0 COMMENT '有使用(0否,1是)',
  `is_lock` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未锁，1：已锁）',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未删，1：已删）',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '更新者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `page_item_id`(`page_item_id`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `update_time`(`update_time`) USING BTREE,
  INDEX `status`(`status`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '一对一' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_universal_item_tab
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_item_tab`;
CREATE TABLE `w_universal_item_tab`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `page_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '[冗]页面id',
  `page_item_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'page_item表的id',
  `tab_key` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '切换key',
  `tab_title` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '切换标题',
  `show_statics` tinyint(1) NULL DEFAULT 0 COMMENT '是否展示统计数量',
  `pc_page_key` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'PC端的链接页面id',
  `param` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '参数',
  `sort` int(11) NULL DEFAULT 1000 COMMENT '排序',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `has_used` tinyint(1) NULL DEFAULT 0 COMMENT '有使用(0否,1是)',
  `is_lock` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未锁，1：已锁）',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未删，1：已删）',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '更新者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `update_time`(`update_time`) USING BTREE,
  INDEX `status`(`status`) USING BTREE,
  INDEX `page_item_id`(`page_item_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = 'tab切换' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_universal_item_table
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_item_table`;
CREATE TABLE `w_universal_item_table`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `page_item_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'page_item表的id',
  `subitem_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '子项id；表中表；对应multi',
  `label` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '' COMMENT '字段名',
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '' COMMENT '字段键名',
  `type` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '{\r\n\"hidden\":\"隐藏域\",\r\n\"text\":\"单行文字\",\r\n\"enum\":\"枚举\",\r\n\"image\":\"上传图片\",\r\n\"date\":\"日期\",\r\n\"datetime\":\"日期+时间\",\r\n\"textarea\":\"文本框\",\r\n\"editor\":\"编辑器\",\r\n\"number\":\"数值\",\r\n\"dynenum\":\"动态枚举\",\r\n\"10\":\"搜索选择框(文本)\",\r\n\"11\":\"搜索选择框(枚举)\",\r\n\"link\":\"跳转链接\",\r\n\"13\":\"链接图片\",\r\n\"14\":\"一级复选(中间表)\",\r\n\"15check\":\"二级复选(中间表)\",\r\n\"16switch\":\"开关\",\r\n\"17\":\"时间框\",\r\n\"18\":\"关联表\",\r\n\"19\":\"密码\",\r\n\"20\":\"联表数据\",\r\n\"21\":\"月日时分\",\r\n\"22\":\"百分比\",\r\n\"23\":\"上传附件\",\r\n\"24\":\"随机串(8位)\",\r\n\"25\":\"随机串(16位)\",\r\n\"26\":\"随机串(32位)\",\r\n\"27\":\"不可编辑框\",\r\n\"28\":\"后台换行设置(行颜色条件)\",\r\n\"29\":\"逗号分隔换行显示\",\r\n\"30\":\"链接二维码\",\r\n\"31-listedit\":\"列表编辑\",\r\n\"99\":\"混合字段\"}',
  `option` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '选项json',
  `forcus_title` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '20220928焦点展示字段',
  `update_url` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '更新地址：适用于switch',
  `width` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `class` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '20220702',
  `show_condition` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '20221015：展示条件',
  `pop_page` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '弹窗页面key',
  `pop_param` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '弹窗页面参数',
  `sortable` tinyint(1) NULL DEFAULT 0 COMMENT '表单是否可排序：0否；1是',
  `conf` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '20220721:表单配置',
  `is_export` tinyint(1) NULL DEFAULT 1 COMMENT '20220808:是否导出字段?',
  `sort` int(11) NULL DEFAULT 1000 COMMENT '排序',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `has_used` tinyint(1) NULL DEFAULT 0 COMMENT '有使用(0否,1是)',
  `is_lock` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未锁，1：已锁）',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未删，1：已删）',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '更新者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `page_item_id`(`page_item_id`) USING BTREE,
  INDEX `type`(`type`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `update_time`(`update_time`) USING BTREE,
  INDEX `name`(`name`) USING BTREE,
  INDEX `status`(`status`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '字段明细表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_universal_item_user_info
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_item_user_info`;
CREATE TABLE `w_universal_item_user_info`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `page_item_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'page_item表的id',
  `background_img` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '背景图',
  `user_url` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '用户信息url',
  `ext_show` tinyint(1) NULL DEFAULT 0 COMMENT '会员续费？web端',
  `qr_show` tinyint(1) NULL DEFAULT 0 COMMENT '二维码展示？',
  `msg_show` tinyint(1) NULL DEFAULT 0 COMMENT '消息展示？',
  `sign_show` tinyint(1) NULL DEFAULT 0 COMMENT '签到展示？',
  `account_show` tinyint(1) NULL DEFAULT NULL COMMENT '20220516账户信息展示？',
  `sort` int(11) NULL DEFAULT 1000 COMMENT '排序',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `has_used` tinyint(1) NULL DEFAULT 0 COMMENT '有使用(0否,1是)',
  `is_lock` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未锁，1：已锁）',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未删，1：已删）',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '更新者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `page_item_id`(`page_item_id`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `update_time`(`update_time`) USING BTREE,
  INDEX `status`(`status`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户信息' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_universal_page
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_page`;
CREATE TABLE `w_universal_page`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `group_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '页面分组（菜单有用）',
  `bind_company_id` int(11) NULL DEFAULT NULL COMMENT '绑定公司（如有值，只有匹配时才出数据[避免客户定制串页]）',
  `type` tinyint(1) NULL DEFAULT NULL COMMENT '类型：1业务；2系统',
  `cate` varchar(8) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '页面分类，wePub公众号；weApp小程序',
  `page_key` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '页面key',
  `page_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '页面名称',
  `api_url` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'api接口路径：有配置拿配置；没配置取默认',
  `param_get` tinyint(1) NULL DEFAULT NULL COMMENT '20220705:是否使用非id的参数请求url',
  `share_img` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '分享图标',
  `share_desc` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '分享描述',
  `layer_width` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '20220624:弹窗宽度，未传时的默认值',
  `layer_height` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '20220624:弹窗高度，未传时的默认值',
  `auth_check` tinyint(1) NULL DEFAULT 0 COMMENT '20230308:开启权限校验',
  `sort` int(11) NULL DEFAULT 1000 COMMENT '排序',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `has_used` tinyint(1) NULL DEFAULT 0 COMMENT '有使用(0否,1是)',
  `is_lock` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未锁，1：已锁）',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未删，1：已删）',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '更新者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `page_key`(`page_key`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `update_time`(`update_time`) USING BTREE,
  INDEX `status`(`status`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '页面表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_universal_page_item
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_page_item`;
CREATE TABLE `w_universal_page_item`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `page_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '页面id',
  `flex_item_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'PC端用',
  `item_key` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '项目key',
  `span` int(11) NULL DEFAULT 24 COMMENT 'element-ui 布局宽度；24等分',
  `data_url` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '数据来源接口地址；type为list，form时使用,\r\nlist为分页数据，form为表单数据',
  `param` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '传递参数',
  `conf` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '20220430项目配置',
  `merge_param` tinyint(1) NULL DEFAULT 0 COMMENT '合并参数?0否；1是',
  `show_condition` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '显示条件',
  `value` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '值；\r\n当为grid宫格时，表示每行格数\r\n当为list时，表示查询id的字段(TODO优化)\r\n当为form时，有值表示无id也从接口取数据\r\n当为tab时，表示绑定dataData的key\r\n当为menu时，表示当前页面的pageKey',
  `height` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '板块高度：一般用于表格',
  `class` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '样式',
  `title` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '标题：可选',
  `title_class` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '标题样式',
  `auth_check` tinyint(1) NULL DEFAULT 0 COMMENT '0825:是否校验权限',
  `sort` int(11) NULL DEFAULT 1000 COMMENT '排序',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `has_used` tinyint(1) NULL DEFAULT 0 COMMENT '有使用(0否,1是)',
  `is_lock` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未锁，1：已锁）',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未删，1：已删）',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '更新者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `update_time`(`update_time`) USING BTREE,
  INDEX `status`(`status`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '页面项目表-负责页面顺序位置' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_universal_page_log
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_page_log`;
CREATE TABLE `w_universal_page_log`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `company_id` int(11) NULL DEFAULT NULL COMMENT '端口id',
  `page_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '页面id',
  `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '0830:访问ip',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `page_id`(`page_id`) USING BTREE,
  INDEX `creater`(`creater`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '页面访问日志' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_universal_structure
-- ----------------------------
DROP TABLE IF EXISTS `w_universal_structure`;
CREATE TABLE `w_universal_structure`  (
  `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `pid` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `page_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '[冗]页面id',
  `page_item_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'page_item表的id',
  `element_describe` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '元素描述',
  `element_type` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '页面项类型，div,span,view等',
  `base_class` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '基础样式：flex-20220920:给小程序用(组件多一层)',
  `element_class` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '页面项样式：flex1',
  `field_class` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '字段样式：f-red 20220926',
  `sub_class` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '小程序：字段子样式20221231',
  `field_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '0902：字段标题',
  `field_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '元素字段名',
  `field_type` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'text' COMMENT '字段的类型',
  `unit_str` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '20220926:后缀字串（一般放单位）',
  `option` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '20220922:选项',
  `show_condition` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '20220926:显示条件',
  `sort` int(11) NULL DEFAULT 1000 COMMENT '排序',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `has_used` tinyint(1) NULL DEFAULT 0 COMMENT '有使用(0否,1是)',
  `is_lock` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未锁，1：已锁）',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '锁定（0：未删，1：已删）',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '创建者，user表',
  `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '更新者，user表',
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE,
  INDEX `update_time`(`update_time`) USING BTREE,
  INDEX `status`(`status`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '页面项目的构造，常见于列表，详情等特殊定制场景' ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
