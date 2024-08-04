<?php

namespace xjryanse\universal\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\Arrays;
use xjryanse\universal\service\UniversalStructureService;
use Exception;

/**
 * 列表
 */
class UniversalItemListService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;


// 静态模型：配置式数据表
    use \xjryanse\traits\StaticModelTrait;
    use \xjryanse\universal\traits\UniversalTrait;
    
    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\universal\\model\\UniversalItemList';

    protected static $itemKey = 'list';

    use \xjryanse\universal\service\itemList\DimTraits;
    use \xjryanse\universal\service\itemList\FieldTraits;

    public static function extraDetails($ids) {
        return self::commExtraDetails($ids, function($lists) use ($ids) {
                    $structCountArr = UniversalStructureService::groupBatchCount('page_item_id', array_column($lists, 'page_item_id'));

                    foreach ($lists as &$v) {
                        // 通用表单结构数量
                        $v['structCount'] = Arrays::value($structCountArr, $v['page_item_id'], 0);
                    }
                    return $lists;
                });
    }

    /**
     * 必有方法
     * 一对一
     */
    public static function optionArr($pageItemId) {
        $con[] = ['page_item_id', '=', $pageItemId];
        $con[] = ['status', '=', 1];
        $res = self::staticConFind($con);
        //$res = self::find( $con );
        // 20230318:增加collaspe
        if (in_array($res['item_style'], ['common', 'collapse'])) {
            //TODO替换为commStruc：itemDetail中使用
            $res['option'] = UniversalStructureService::getItemStructure($pageItemId);
        }

        $res['update_param'] = json_decode($res['update_param']);
        return $res;
    }

    public static function downLoadRemoteConf($pageItemId, $newPageItemId) {
        return self::universalSysItemsDownload($pageItemId, $newPageItemId);
    }

    /**
     * 钩子-保存前
     */
    public static function extraPreSave(&$data, $uuid) {
        
    }

    /**
     * 钩子-保存后
     */
//    public static function extraAfterSave(&$data, $uuid) {
//
//    }

    /**
     * 钩子-更新前
     */
    public static function extraPreUpdate(&$data, $uuid) {
        
    }

    /**
     * 钩子-更新后
     */
//    public static function extraAfterUpdate(&$data, $uuid) {
//
//    }

    /**
     * 钩子-删除前
     */
    public function extraPreDelete() {
        
    }

    /**
     * 钩子-删除后
     */
    public function extraAfterDelete() {
        
    }

    /**
     * 20230914：按页面项删除字段
     * @param type $pageItemId
     */
    public static function delRecur($pageItemId){
        // 提取页面项字段
        $fieldArr = self::dimListByPageItemId($pageItemId);
        // 循环，替换，更新
        foreach($fieldArr as $v){
            // 删除
            self::getInstance($v['id'])->deleteRam();
        }
        // 20230914：删结构
        UniversalStructureService::delRecur($pageItemId);
        
        return true;
    }

    /**
     * 
     * @param type $oPageItemId 原页面项
     * @param type $nPageItemId 新页面项
     * @param type $fieldArr    标准字段
     * @throws Exception
     */
    public static function copyPageItemReplaceField($oPageItemId, $nPageItemId, $fieldArr, $replaceArr = []){

        $con[]  = ['page_item_id','=',$oPageItemId];
        $con[]  = ['status','=',1];
        $list   = self::lists($con);
        $listArr = $list && !is_array($list) ? $list->toArray() : $list;
        
        $keys       = ['id','creater','updater','create_time','update_time'];
        $arr = [];
        foreach($listArr as &$v){

            $v = Arrays::strReplace($v, $replaceArr);
            $tmp                    = Arrays::unset($v, $keys);
            $tmp['page_item_id']    = $nPageItemId;
            $arr[]                  = $tmp;
        }

        $res = self::saveAllRam($arr);
        // 复制页面结构 
        UniversalStructureService::copyPageItemReplaceField($oPageItemId, $nPageItemId, $fieldArr, $replaceArr);
        
        return $res;

    }
    
}
