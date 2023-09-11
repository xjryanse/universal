<?php
namespace xjryanse\universal\model;

/**
 * 宫格
 */
class UniversalItemGrid extends Base
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
    
    public static $picFields = ['icon_pic'];
    
    public function setIconPicAttr($value) {
        return self::setImgVal($value);
    }
    //轮播
    public function getIconPicAttr($value) {
        return self::getImgVal($value);
    }

}