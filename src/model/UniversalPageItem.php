<?php
namespace xjryanse\universal\model;

/**
 * 万能表单页面项目
 */
class UniversalPageItem extends Base
{
    //20230728 是否将数据缓存到文件
    public static $cacheToFile = true;
    
    use \xjryanse\traits\ModelUniTrait;
    // 20230516:数据表关联字段
    public static $uniFields = [
        [
            'field'     =>'page_id',
            'uni_name'  =>'universal_page',
            'uni_field' =>'id',
            'del_check' => true,
            'del_msg'   => '请先删除{$count}个页面项'
        ],
    ];

}