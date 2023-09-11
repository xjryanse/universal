<?php
namespace xjryanse\universal\model;

/**
 * 20220616灵活布局
 */
class UniversalItemFlex extends Base
{
    //20230728 是否将数据缓存到文件
    public static $cacheToFile = true;
    
    public static $picFields = ['icon_pic'];
    
    public function setIconPicAttr($value) {
        return self::setImgVal($value);
    }
    //轮播
    public function getIconPicAttr($value) {
        return self::getImgVal($value);
    }
}