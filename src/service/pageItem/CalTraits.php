<?php

namespace xjryanse\universal\service\pageItem;

/**
 * 触发器
 */
trait CalTraits{
    /*
     * 计算子表名
     */
    public static function calSubTableName($itemKey){
        $prefix     = config('database.prefix');
        $tableName  = $prefix . 'universal_item_' . $itemKey;
        return $tableName;
    }
}
