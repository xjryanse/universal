<?php
namespace xjryanse\universal\model;

/**
 * 详情
 */
class UniversalItemDetail extends Base
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