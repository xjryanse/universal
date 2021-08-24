<?php
namespace xjryanse\universal\model;

/**
 * 页面轮播
 */
class UniversalItemBanner extends Base
{
    public function setBannerPicAttr($value) {
        return self::setImgVal($value);
    }
    //轮播
    public function getBannerPicAttr($value) {
        return self::getImgVal($value);
    }
}