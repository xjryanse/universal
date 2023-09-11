<?php
namespace xjryanse\universal\model;

/**
 * 表格
    CREATE TABLE `w_universal_item_table` (
      `id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
      `page_item_id` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT 'page_item表的id',
      `subitem_id` char(19) DEFAULT NULL COMMENT '子项id；表中表；对应multi',
      `label` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '' COMMENT '字段名',
      `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '' COMMENT '字段键名',
      `type` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT '{\r\n"hidden":"隐藏域",\r\n"text":"单行文字",\r\n"enum":"枚举",\r\n"image":"上传图片",\r\n"date":"日期",\r\n"datetime":"日期+时间",\r\n"textarea":"文本框",\r\n"editor":"编辑器",\r\n"number":"数值",\r\n"dynenum":"动态枚举",\r\n"10":"搜索选择框(文本)",\r\n"11":"搜索选择框(枚举)",\r\n"link":"跳转链接",\r\n"13":"链接图片",\r\n"14":"一级复选(中间表)",\r\n"15check":"二级复选(中间表)",\r\n"16switch":"开关",\r\n"17":"时间框",\r\n"18":"关联表",\r\n"19":"密码",\r\n"20":"联表数据",\r\n"21":"月日时分",\r\n"22":"百分比",\r\n"23":"上传附件",\r\n"24":"随机串(8位)",\r\n"25":"随机串(16位)",\r\n"26":"随机串(32位)",\r\n"27":"不可编辑框",\r\n"28":"后台换行设置(行颜色条件)",\r\n"29":"逗号分隔换行显示",\r\n"30":"链接二维码",\r\n"31-listedit":"列表编辑",\r\n"99":"混合字段"}',
      `option` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '选项json',
      `forcus_title` varchar(32) DEFAULT NULL COMMENT '20220928焦点展示字段',
      `update_url` text CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '更新地址：适用于switch',
      `fixed` varchar(32) DEFAULT NULL COMMENT '20230425固定列',
      `width` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
      `class` varchar(64) DEFAULT NULL COMMENT '20220702',
      `show_condition` text COMMENT '20221015：展示条件',
      `pop_page` text COMMENT '弹窗页面key',
      `pop_param` text COMMENT '弹窗页面参数',
      `sortable` tinyint(1) DEFAULT '0' COMMENT '表单是否可排序：0否；1是',
      `conf` text COMMENT '20220721:表单配置',
      `is_export` tinyint(1) DEFAULT '1' COMMENT '20220808:是否导出字段?',
      `sort` int(11) DEFAULT '1000' COMMENT '排序',
      `status` tinyint(1) DEFAULT '1' COMMENT '状态(0禁用,1启用)',
      `has_used` tinyint(1) DEFAULT '0' COMMENT '有使用(0否,1是)',
      `is_lock` tinyint(1) DEFAULT '0' COMMENT '锁定（0：未锁，1：已锁）',
      `is_delete` tinyint(1) DEFAULT '0' COMMENT '锁定（0：未删，1：已删）',
      `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '备注',
      `creater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT '创建者，user表',
      `updater` char(19) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT '更新者，user表',
      `create_time` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
      `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
      PRIMARY KEY (`id`) USING BTREE,
      KEY `page_item_id` (`page_item_id`) USING BTREE,
      KEY `type` (`type`) USING BTREE,
      KEY `create_time` (`create_time`) USING BTREE,
      KEY `update_time` (`update_time`) USING BTREE,
      KEY `name` (`name`) USING BTREE,
      KEY `status` (`status`) USING BTREE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='字段明细表';
 * 
 */
class UniversalItemTable extends Base
{
    //20230728 是否将数据缓存到文件
    public static $cacheToFile = true;
    
    use \xjryanse\traits\ModelUniTrait;
    // 20230516:数据表关联字段
    public static $uniFields = [
        [
            'field'     =>'page_item_id',
            'uni_name'  =>'universal_page_item',
            'uni_field' =>'id',
            'del_check' => true
        ],
    ];
}