<?php
namespace xjryanse\universal\model;

/**
 * tab标签
 */
class UniversalItemTab extends Base
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