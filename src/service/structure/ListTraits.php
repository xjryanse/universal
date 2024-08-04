<?php
namespace xjryanse\universal\service\structure;

use Exception;
use xjryanse\logic\Arrays2d;
use xjryanse\logic\Arrays;
/**
 * 分页复用列表
 */
trait ListTraits{
        /**
     * 20230806：uniqid，组织树状
     */
    public static function listStructureTree($param) {

        $pageItemId   = Arrays::value($param,'page_item_id');
        $con        = [];
        $con[]      = ['page_item_id','=',$pageItemId];

        $all    = self::where($con)->order('sort')->select();
        $allArr = $all ? $all->toArray() : [];

        return Arrays2d::makeTree($allArr,'','pid','subLists');
    }

}
