<?php
namespace xjryanse\universal\model;

/**
 * 用户个人信息
 */
class UniversalItemUserInfo extends Base
{
    // 分享图标
    public function setBackgroundImgAttr($value) {
        return self::setImgVal($value);
    }
    // 分享图标
    public function getBackgroundImgAttr($value) {
        return self::getImgVal($value);
    }

}