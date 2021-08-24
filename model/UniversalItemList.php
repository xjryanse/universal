<?php
namespace xjryanse\universal\model;

/**
 * 列表
 */
class UniversalItemList extends Base
{
    public function setIconPicAttr($value) {
        return self::setImgVal($value);
    }
    //轮播
    public function getIconPicAttr($value) {
        return self::getImgVal($value);
    }

}