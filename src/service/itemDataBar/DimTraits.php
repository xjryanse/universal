<?php

namespace xjryanse\universal\service\itemDataBar;

/**
 * 按维度查询
 */
trait DimTraits{
    /*
     * page_id维度列表
     */
    public static function dimListByPageItemId($pageItemId){
        $con    = [];
        $con[]  = ['page_item_id','=',$pageItemId];
        return self::staticConList($con);
    }
}
