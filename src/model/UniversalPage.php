<?php
namespace xjryanse\universal\model;

/**
 * 页面表
 */
class UniversalPage extends Base
{
    // 分享图标
    public function setShareImgAttr($value) {
        return self::setImgVal($value);
    }
    // 分享图标
    public function getShareImgAttr($value) {
        return self::getImgVal($value);
    }

}