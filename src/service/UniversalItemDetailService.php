<?php

namespace xjryanse\universal\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\system\service\SystemColumnListService;
use xjryanse\uniform\service\UniformTableService;
use xjryanse\uniform\service\UniformUniversalItemDetailService;
use xjryanse\logic\Strings;
use xjryanse\logic\Arrays2d;
use xjryanse\system\service\columnlist\Dynenum;
use think\facade\Request;

/**
 * 列表
 */
class UniversalItemDetailService extends Base implements MainModelInterface {

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
    protected static $mainModelClass = '\\xjryanse\\universal\\model\\UniversalItemDetail';
    
    use \xjryanse\universal\service\itemDetail\DimTraits;
    use \xjryanse\universal\service\itemDetail\FieldTraits;
    
    protected static $itemKey = 'detail';    
    
    public static function extraDetails($ids) {
        return self::commExtraDetails($ids, function($lists) use ($ids) {
                    return $lists;
                },true);
    }
    /**
     * 必有方法
     * @param type $pageItemId
     * @param type $subKey      20230329:兼容万能表单
     * @return type
     */
    public static function optionArr($pageItemId, $subKey = '') {
        $con[] = ['page_item_id', '=', $pageItemId];
        $con[] = ['status', '=', 1];
        $resRaw = self::staticConList($con, '', 'sort');
        //$res = self::lists( $con ,'sort','id,label,show_label,field,type,option,show_condition,class,span,layer_url');
        // 处理MONTH_DAY等特殊字串
        $res = self::listDeal($resRaw, $subKey);

        $btns = UniversalItemBtnService::optionArr($pageItemId);
        
        foreach ($res as &$v) {
            $v = self::addOpt($v);
            // 20231015表单后向按钮
            $v['afterBtns'] = $btns ? Arrays2d::listFilter($btns, [['subitem_id','=',$v['id']]]) : [];
        }
        return $res;
    }

    public static function listDeal($uList, $subKey = '') {
        foreach ($uList as $k => &$v) {
            // 20230329:万能联动表单
            if ($v['type'] == 'uniform') {
                $tableId = UniformTableService::tableToId($subKey);
                $data = UniformUniversalItemDetailService::getOptionArr($tableId, $v['field']);
                array_splice($uList, $k, 1, $data);
            }
        }

        return $uList;
    }

    /**
     * 子级
     * @param type $subItemId
     * @return type
     */
    public static function subOptionArr($subItemId) {
        $con[] = ['subitem_id', '=', $subItemId];
        $con[] = ['status', '=', 1];

        $res = self::lists($con, 'sort', 'id,label,show_label,field,type,option,show_condition,class,span,layer_url');
        // 20231224？？？合理吗？
        $btns = UniversalItemBtnService::optionArr($subItemId);
        foreach ($res as &$v) {
            $v = self::addOpt($v);
            // 20231015表单后向按钮
            $v['afterBtns'] = $btns ? Arrays2d::listFilter($btns, [['subitem_id','=',$v['id']]]) : [];
        }
        return $res;
    }

    private static function addOpt($v) {
        //展示条件
        $v['show_condition'] = json_decode($v['show_condition']);
        //参数替换
        $data['comKey'] = session(SESSION_COMPANY_KEY);
        // 2022-12-06
        $data['domain'] = Request::domain(true);

        $v['option'] = Strings::dataReplace($v['option'], $data);

        if (in_array($v['type'], ['enum', 'dynenum', 'radio', 'tplset'])) {
            if ($v['type'] == 'radio') {
                // radio为枚举
                $v['option'] = SystemColumnListService::getOption('enum', $v['option']);
            } else {
                $v['option'] = SystemColumnListService::getOption($v['type'], $v['option']);
            }
        } else {
            $v['option'] = Strings::isJson($v['option']) ? json_decode($v['option']) : $v['option'];
        }
        if (in_array($v['type'], ['array', 'form','subData'])) {
            //子表单
            $v['subItems'] = self::subOptionArr($v['id']);
        }
        // 20230315：增加通用表单框
        if (in_array($v['type'], ['common', 'flow'])) {
            // 表单页面结构
            $v['commStruc'] = UniversalStructureService::getItemStructure($v['id']);
        }
        return $v;
    }

    /**
     * 传一堆字段数组保存
     * @param type $pageItemId
     * @param type $fields
     */
    public static function saveField($pageItemId, $fields, $span = 6) {
        self::checkTransaction();
        $dataArr = [];
        foreach ($fields as &$field) {
            $tmp = [];
            $tmp['page_item_id'] = $pageItemId;
            $tmp['label'] = $field;
            $tmp['field'] = $field;
            $tmp['type'] = 'text';
            $tmp['span'] = $span;
            $dataArr[] = $tmp;
        }
        $res = self::saveAll($dataArr);
        return $res;
    }

    /**
     * 20230331
     * @param type $pageItemId
     * @param type $newPageItemId
     * @return boolean
     */    
    public static function downLoadRemoteConf($pageItemId, $newPageItemId) {
        return self::universalSysItemsDownload($pageItemId, $newPageItemId);
    }

    /**
     * 20230717:提取上传图片字段
     * @param type $pageItemId      
     * @param type $subKey          table_no
     * @param type $type            uplimage
     * @return type
     */
    public static function typeFields($pageItemId, $subKey, $type ){
        $options = self::optionArr($pageItemId, $subKey);
        $arr = [];
        foreach($options as $v){
            if($v['type'] == $type){
                $arr[] = $v['field'];
            }
        }
        return $arr;
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
     * 20230717:提取上传图片字段
     * @param type $pageItemId      
     * @param type $subKey          table_no
     * @param type $type            uplimage
     * @return type
     */
    public static function getFieldsByPageItemId($pageItemId, $subKey, $type ){
        $options = self::optionArr($pageItemId, $subKey);
        $arr     = [];
        foreach($options as $v){
            if($v['type'] == $type){
                $arr[] = $v['field'];
            }
        }
        return $arr;
    }

    
    /**
     * 20230906：转换成标准化字段，用于后台展示对比
     */
    public static function standardFields($pageItemId){
        // $arr[] = ['page_item_id'=>'1111','itemType'=>'detail','label'=>'标签','field'=>'test','type'=>'text'];
        
        $con    = [];
        $con[]  = ['page_item_id','=',$pageItemId];
        $con[]  = ['status','=',1];

        $lists = self::staticConList($con);
        $arr = [];
        foreach($lists as &$v){
            $tmp    = ['page_item_id'=>$pageItemId,'itemType'=>'detail','label'=>$v['label'],'field'=>$v['field'],'type'=>$v['type']];
            $arr[]  = $tmp;
        }

        return $arr;
    }
    /*
     * 标准字段转本表字段
     */
    public static function standardFieldToThis($pageItemId, $fieldsArr = []){
        $arr = [];
        foreach($fieldsArr as $v){
            $tmp = $v;
            $tmp['page_item_id']    = $pageItemId;

            $arr[] = $tmp;
        }
        return $arr;
    }
    
        /**
     * 20230413：获取动态枚举配置
     * @param type $pageItemId
     * 'user_id'    =>'table_name=w_user&key=id&value=username'
     * 'goods_id'   =>'table_name=w_goods&key=id&value=goods_name'
     */
    public static function dynArrs($pageItemId) {
        $con[] = ['page_item_id', '=', $pageItemId];
        $lists = self::staticConList($con);
        if (!$lists) {
            // 系统远端提取
            // $lists = self::sysItems($pageItemId);
            $lists = self::universalSysItems($pageItemId);
        }
        $cone[] = ['status', '=', 1];
        $cone[] = ['type', '=', 'dynenum'];
        $listEE = Arrays2d::listFilter($lists, $cone);

        return array_column($listEE, 'option', 'field');
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
}
