<?php
namespace xjryanse\universal\model;

/**
 * 页面轮播
 */
class UniversalItemBanner extends Base
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
    
    public static $picFields = ['banner_pic'];
    
    public function setBannerPicAttr($value) {
        return self::setImgVal($value);
    }
    //轮播
    public function getBannerPicAttr($value) {
        return self::getImgVal($value);
    }
}