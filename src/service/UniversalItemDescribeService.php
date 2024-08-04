<?php

namespace xjryanse\universal\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 数据条
 * 
 */
class UniversalItemDescribeService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;


// 静态模型：配置式数据表
    use \xjryanse\traits\ObjectAttrTrait;
    use \xjryanse\traits\StaticModelTrait;
    use \xjryanse\universal\traits\UniversalTrait;
    
    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\universal\\model\\UniversalItemDescribe';
    
    use \xjryanse\universal\service\itemDescribe\DimTraits;
    use \xjryanse\universal\service\itemDescribe\FieldTraits;
    
    protected static $itemKey = 'describe';
    
    public static function extraDetails($ids) {
        return self::commExtraDetails($ids, function($lists) use ($ids) {
                    return $lists;
                },true);
    }
    /**
     * 必有方法
     */
    public static function optionArr($pageItemId) {
        $con[] = ['page_item_id', '=', $pageItemId];
        $con[] = ['status', '=', 1];
        $res = self::staticConList($con, '', 'sort');
        //$res = self::lists($con, 'sort');
        return $res;
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
     * 20230331
     * @param type $options
     * @param type $newPageItemId
     * @return boolean
     */
    public static function downLoadRemoteConf($options, $newPageItemId) {
        return self::universalSysItemsDownload($pageItemId, $newPageItemId);
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
        return true;
    }
    
}
