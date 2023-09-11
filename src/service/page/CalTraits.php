<?php

namespace xjryanse\universal\service\page;

/**
 * 字段复用列表
 */
trait CalTraits{

    /**
     * 计算页面是否存在
     */
    public static function isPageKeyExist($pageKey){
        $con    = [];
        $con[]  = ['page_key','=',$pageKey];

        return self::staticConFind($con) ? 1: 0;
    }

}
