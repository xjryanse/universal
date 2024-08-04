<?php

namespace xjryanse\universal\service\pageItem;

/**
 * 按维度查询
 */
trait DimTraits{

    /*
     * page_id维度列表
     */
    public static function dimListByPageId($pageId){
        $con    = [];
        $con[]  = ['page_id','=',$pageId];
        return self::staticConList($con);
    }
    

}
