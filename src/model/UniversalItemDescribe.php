<?php
namespace xjryanse\universal\model;

/**
 * 信息描述
 */
class UniversalItemDescribe extends Base
{   
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
    
    public static $picFields = ['home_img'];
    
    public function setHomeImgAttr($value) {
        return self::setImgVal($value);
    }
    //轮播
    public function getHomeImgAttr($value) {
        return self::getImgVal($value);
    }
}