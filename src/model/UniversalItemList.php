<?php
namespace xjryanse\universal\model;

/**
 * 列表
 */
class UniversalItemList extends Base
{
    public static $picFields = ['icon_pic'];
    
    public function setIconPicAttr($value) {
        return self::setImgVal($value);
    }
    //轮播
    public function getIconPicAttr($value) {
        return self::getImgVal($value);
    }

}