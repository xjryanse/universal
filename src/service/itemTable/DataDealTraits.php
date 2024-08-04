<?php

namespace xjryanse\universal\service\itemTable;

use xjryanse\logic\Strings;
use xjryanse\logic\Arrays;
/**
 * 按维度查询
 */
trait DataDealTraits{
    
    /**
     * 202312121：列表添加计算属性数据
     * @param type $pageItemId
     * @param type $pgLists
     * @return type
     */
    public static function arrListSumAddCalc($pageItemId, &$pgLists){
        $sumData = Arrays::value($pgLists,'sumData') ? : [];
        // 20231221:增加计算属性
        $arr = self::calcFieldArr($pageItemId);
        foreach($arr as &$ve){
            if(!$ve['calc_method']){
                continue;
            }
            $methodStr = Strings::dataReplace($ve['calc_method'], $sumData);
            $pgLists['sumData'][$ve['name']] = eval('return '.$methodStr.';');
        }
        return $pgLists;
    }
    
}
