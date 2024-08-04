<?php

namespace xjryanse\universal\service\itemTable;

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
    /**
     * 提取字段数组
     * @param type $pageItemId
     * @return type
     */
    public static function dimFieldsByPageItemId($pageItemId){
        $lists = self::dimListByPageItemId($pageItemId);
        return array_unique(array_column($lists,'name'));
    }

    
}
