<?php
namespace xjryanse\universal\model;

/**
 * 表格形式的表单输入框
 */
class UniversalItemFtableSubitem extends Base
{
    //20230728 是否将数据缓存到文件
    public static $cacheToFile = true;
    
    use \xjryanse\traits\ModelUniTrait;
    // 20230516:数据表关联字段
    public static $uniFields = [
        [
            'field'     =>'ftable_id',
            'uni_name'  =>'universal_item_ftable',
            'uni_field' =>'id',
            'del_check' => true
        ],
    ];
    
    
    public static $uniRevFields = [
        [
            'table'     =>'universal_item_btn',
            'field'     =>'subitem_id',
            'uni_field' =>'id',
            'exist_field'   =>'isUniversalItemBtnExist',
            'condition'     =>[
                // 关联表，即本表
                // 'belong_table'=>'{$uniTable}'
            ]
        ],
        [
            'table'     =>'universal_item_form',
            'field'     =>'subitem_id',
            'uni_field' =>'id',
            'exist_field'   =>'isUniversalItemFormExist',
            'condition'     =>[
                // 关联表，即本表
                // 'belong_table'=>'{$uniTable}'
            ]
        ]
    ];
    
    
}