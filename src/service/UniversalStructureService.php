<?php

namespace xjryanse\universal\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\system\service\columnlist\Dynenum;
use xjryanse\logic\Arrays;
use xjryanse\logic\Arrays2d;
/**
 * 页面表
 */
class UniversalStructureService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;


// 静态模型：配置式数据表
    use \xjryanse\traits\StaticModelTrait;
    use \xjryanse\traits\TreeTrait;
    use \xjryanse\universal\traits\UniversalTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\universal\\model\\UniversalStructure';
    //直接执行后续触发动作
    protected static $directAfter = true;

    use \xjryanse\universal\service\structure\FieldTraits;
    use \xjryanse\universal\service\structure\DimTraits;
    use \xjryanse\universal\service\structure\ListTraits;
    
    /**
     * 20220822:酷酷酷酷酷酷
     * @param type $pageItemId
     * @return type
     */
    public static function getItemStructure($pageItemId) {
        $con[] = ['page_item_id', '=', $pageItemId];
        $con[] = ['status', '=', 1];
        $lists = self::staticConList($con, '', 'sort');
        foreach ($lists as &$v) {
            $v['option'] = self::universalOptionCov($v['field_type'], $v['option']);
            $v['element_class'] = json_decode($v['element_class'], JSON_UNESCAPED_UNICODE) ?: $v['element_class'];
            $v['show_condition'] = json_decode($v['show_condition'], JSON_UNESCAPED_UNICODE);
            if($v['field_type'] == 'btn'){
                // 20240204:todo:合理性？
                $v['btns'] = UniversalItemBtnService::optionArr($v['id']);
            }
            // 20240321:表单
            if($v['field_type'] == 'form'){
                // 20240204:todo:合理性？
                $v['forms'] = UniversalItemFormService::optionArr($v['id']);
            }
        }
        //拼接树状
        $res = self::makeTree($lists, '');

        return $res;
    }
    
    /**
     * 20230607
     * @param type $pageItemId
     * @param type $dataArr     二维数组
     * @return type
     */
    public static function getDynDataListByPageItemIdAndData($pageItemId, $dataArr) {
        $dynArrs = self::dynArrs($pageItemId);
        $res = Dynenum::dynDataList($dataArr, $dynArrs);
        return $res;
    }

    /**
     * 20240317：获取动态枚举配置
     * @param type $pageItemId
     * 'user_id'    =>'table_name=w_user&key=id&value=username'
     * 'goods_id'   =>'table_name=w_goods&key=id&value=goods_name'
     */
    public static function dynArrs($pageItemId) {
        $con[] = ['page_item_id', '=', $pageItemId];
        $lists = self::staticConList($con);
        /* 20240317:暂不支持远端
        if (!$lists) {
            // 系统远端提取
            // $lists = self::sysItems($pageItemId);
            // $lists = self::universalSysItems($pageItemId);
        }
         */
        $cone[] = ['status', '=', 1];
        $cone[] = ['field_type', '=', 'dynenum'];
        $listEE = Arrays2d::listFilter($lists, $cone);
        return array_column($listEE, 'option', 'field_name');
    }
    
    /**
     * 钩子-保存前
     */
    public static function extraPreSave(&$data, $uuid) {
        
    }

    /**
     * 钩子-保存后
     */
    public static function extraAfterSave(&$data, $uuid) {
        
    }

    /**
     * 钩子-更新前
     */
    public static function extraPreUpdate(&$data, $uuid) {
        
    }

    /**
     * 钩子-更新后
     */
    public static function extraAfterUpdate(&$data, $uuid) {
        
    }

    /**
     * 钩子-删除前
     */
    public function extraPreDelete() {
        
    }

    /**
     * 钩子-删除后
     */
    public function extraAfterDelete() {
        $this->universalRoleClear();
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
    
    public static function copyPageItemReplaceField($oPageItemId, $nPageItemId, $fieldArr, $replaceArr = []){
        $lists = self::dimListByPageItemId($oPageItemId);
        //给pageItemId做一下映射
        $reflectIds = [];
        foreach($lists as $v){
            $reflectIds[$v['id']] = self::mainModel()->newId();
        }
        $newLists = [];
        $keys       = ['creater','updater','create_time','update_time'];
        foreach($lists as $v){
            $v['id']            = Arrays::value($reflectIds, $v['id']);
            $v['pid']           = Arrays::value($reflectIds, $v['pid']);
            $v['page_item_id']  = $nPageItemId;
            // 20230914：有字段处理一下
            if($v['field_name']){
                // 弹一个；
                $item = array_shift($fieldArr);
                $v['field_title']   = Arrays::value($item, 'label');
                $v['field_name']    = Arrays::value($item, 'field');
                $v['field_type']    = Arrays::value($item, 'type');
            }

            $newLists[]         = Arrays::unset($v, $keys);
        }

        $res = self::saveAllRam($newLists);
        return $res;
    }

}
