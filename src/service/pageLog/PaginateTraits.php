<?php

namespace xjryanse\universal\service\pageLog;

/**
 * 触发器
 */
trait PaginateTraits{

    /**
     * 页面访问统计
     * @createTime 2023-10-12
     * @param type $con
     */
    public static function paginatePageStatics($con, $order, $perPage, $having, $field, $withSum){
        $resInst = self::mainModel()->where($con)
                ->field('count( 1 ) as times,count(distinct creater) as userNum,page_id')
                ->group('page_id')
                ->order('times desc');

        $res = $resInst->paginate($perPage);
        $resp = $res ? $res->toArray() : [];

        return $resp;
    }

}
