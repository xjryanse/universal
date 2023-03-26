<?php
namespace xjryanse\universal\model;

/**
 * 信息描述
 */
class UniversalItemDescribe extends Base
{
    public static $picFields = ['home_img'];
    
    public function setHomeImgAttr($value) {
        return self::setImgVal($value);
    }
    //轮播
    public function getHomeImgAttr($value) {
        return self::getImgVal($value);
    }
}