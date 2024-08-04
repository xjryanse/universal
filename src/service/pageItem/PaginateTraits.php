<?php

namespace xjryanse\universal\service\pageItem;

use xjryanse\system\service\SystemCateService;
use xjryanse\universal\service\UniversalItemService;
use xjryanse\logic\Arrays2d;
use xjryanse\logic\Arrays;
/**
 * 触发器
 */
trait PaginateTraits{

    /**
     * 20230908:客户配置用
     * @param type $con
     */
    public static function paginateForCustomerConf($con) {
        $lists = self::paginateRaw($con);
        $keys = ['id','page_id','item_key','status'];
        $lists['data'] = Arrays2d::getByKeys($lists['data'], $keys);
        
        $arr = UniversalItemService::where()->column('item_name','item_key');

        foreach($lists['data'] as &$v){
            // TODO更科学的方法：提取配置，来当作页面
            $group              = SystemCateService::columnByGroup( 'pageItemsDefaultPageKey' );
            $pageKey            = isset($group[$v['item_key']]) ? $group[$v['item_key']]['cate_name'] : '';
            // 用于页面显示
            $v['itemName']      = Arrays::value($arr, $v['item_key']);
            $v['listPage']      = $pageKey;
            $v['listPageParam'] = ['page_item_id'=>$v['id']];
        }

        return $lists;
    }

}
