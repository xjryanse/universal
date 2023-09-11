<?php

namespace xjryanse\universal\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\user\service\UserAuthRoleUniversalService;
use xjryanse\uniform\service\UniformTableService;
use xjryanse\logic\Debug;
use xjryanse\logic\DbOperate;
use xjryanse\logic\Arrays;
use xjryanse\logic\Strings;
use think\Db;

/**
 * 万能表单页面项目
 */
class UniversalPageItemService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelQueryTrait;

// 静态模型：配置式数据表
    use \xjryanse\traits\StaticModelTrait;

// 带权限查询
    use \xjryanse\universal\traits\UniversalTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\universal\\model\\UniversalPageItem';
    //直接执行后续触发动作
    protected static $directAfter = true;
    
    use \xjryanse\universal\service\pageItem\TriggerTraits;
    use \xjryanse\universal\service\pageItem\PaginateTraits;

    public static function extraDetails($ids) {
        return self::commExtraDetails($ids, function($lists) use ($ids) {
                    $universalTable = self::getTable();
                    foreach ($lists as &$v) {
                        $v['roleIds'] = UserAuthRoleUniversalService::universalRoleIds($universalTable, $v['id']);
                        $v['roleCount'] = count($v['roleIds']);
                    }
                    return $lists;
                });
    }

    /**
     * 20220524
     * 复制单条项目，不替换子级内容
     * @param type $newPageId
     * @param type $replaceArr      替换字段：['T26N010'=>'T26N011']
     * @return type
     */
    public function copyItem($newPageId, $replaceArr=[]) {
        //【1】保存页面项
        $resp = $this->copyPageItemSave($newPageId);

        $itemKey    = $this->fItemKey();
        // 页面项
        $prefix     = config('database.prefix');
        $tableName  = $prefix . 'universal_item_' . $itemKey;
        $tableClass = DbOperate::getService($tableName);
        if (class_exists($tableClass) && DbOperate::hasField($tableName, 'page_item_id')) {
            //【2】页面项目
            $con[] = ['page_item_id', '=', $this->uuid];
            $items = Db::table($tableName)->where($con)->select();
            foreach ($items as &$v) {
                // 20230910:增加替换字符串
                $v = Arrays::strReplace($v, $replaceArr);
                $v['id']            = self::mainModel()->newId();
                $v['page_id']       = $newPageId;
                $v['page_item_id']  = Arrays::value($resp,'id');
            }
            $tableClass::saveAll($items);
        }

        return $resp;
    }
    
    protected function itemKeyCopy($itemKey, $newPageItemId, $replaceArr=[]) {
        // 页面项
        $prefix     = config('database.prefix');
        $tableName  = $prefix . 'universal_item_' . $itemKey;
        $tableClass = DbOperate::getService($tableName);
        if (!class_exists($tableClass) || DbOperate::hasField($tableName, 'page_item_id')) {
            
        }
        //【2】页面项目
        $con[] = ['page_item_id', '=', $this->uuid];
        $items = Db::table($tableName)->where($con)->select();
        foreach ($items as &$v) {
            // 20230910:增加替换字符串
            $v                  = Arrays::strReplace($v, $replaceArr);
            $v['id']            = self::mainModel()->newId();
            // $v['page_id']       = $newPageId;
            $v['page_item_id']  = $newPageItemId;
        }
        return $tableClass::saveAll($items);
    }
    /**
     * 复制页面，替换字段
     * @param type $newPageId       新页面
     * @param type $fieldArr        标准字段入参
     * @param type $replaceArr      替换字段：['T26N010'=>'T26N011']
     * @return bool
     */
    public function copyItemReplaceField($newPageId, $fieldArr = [], $replaceArr = []) {
        //【1】保存页面项
        $resp = $this->copyPageItemSave($newPageId, $replaceArr);

        $pageItemId = Arrays::value($resp,'id');
        $itemKey    = $this->fItemKey();
        $res = $this->itemKeyCopyReplace($itemKey, $pageItemId, $fieldArr, $replaceArr);
        if($itemKey == 'table'){
            //表格的话，增加按钮
            $this->itemKeyCopy('btn', $pageItemId, $replaceArr);
        }
        return $res;
    }
    
    protected function itemKeyCopyReplace($itemKey, $pageItemId, $fieldArr = [], $replaceArr = []){
        $prefix     = config('database.prefix');
        $tableName  = $prefix . 'universal_item_' . $itemKey;
        $tableClass = DbOperate::getService($tableName);

        if (!class_exists($tableClass) || !DbOperate::hasField($tableName, 'page_item_id')) {
            return false;
        }
        // 20230910 标准字段的转换逻辑
        if(method_exists($tableClass, 'standardFieldToThis')){
            // 标准字段转各表保存
            $fieldArr =  $tableClass::standardFieldToThis($pageItemId, $fieldArr);
        }
        // 20230911 不替换字段的提取逻辑
        if(method_exists($tableClass, 'copyKeepFields')){
            // 标准字段转各表保存
            $tmpArr =  $tableClass::copyKeepFields($this->uuid, $pageItemId);
            $fieldArr = array_merge($fieldArr, $tmpArr);
        }
        
        //字段处理和保存
        foreach($fieldArr as &$v){
            // 20230910:增加替换字符串
            $v = Arrays::strReplace($v, $replaceArr);

            $v['id']            = self::mainModel()->newId();
            // $v['page_id']       = $newPageId;
            $v['page_item_id']  = $pageItemId;
        }

        return $tableClass::saveAll($fieldArr);
    }
    /**
     * 复制页面的页面项基本数据保存
     */
    protected function copyPageItemSave($newPageId, $replaceArr = []){
        $infoRaw = $this->get();
        if (!$infoRaw) {
            throw new Exception('页面项' . $this->uuid . '不存在');
        }
        //【1】保存页面项
        $infoArrRaw = is_array($infoRaw) ? $infoRaw : $infoRaw->toArray();

        // 20230910:增加替换字符串
        $infoArr = Arrays::strReplace($infoArrRaw, $replaceArr);

        $hideKeys           = DbOperate::keysForHide();
        $info               = Arrays::hideKeys($infoArr, $hideKeys);
        $newPageItemId      = self::mainModel()->newId();
        $info['id']         = $newPageItemId;
        $info['page_id']    = $newPageId;
        return self::save($info);
    }

    /*
     * 20230331下载远端的pageItem
     * @param type $pageItems       页面项数组
     * @param type $newPageId       新页面id
     * @return type
     * @throws Exception
     */

    public static function downLoadRemotePageItem($pageItems, $newPageId) {
        self::checkTransaction();
        foreach ($pageItems as $item) {
            $sData = $item;
            $newPageItemId = self::mainModel()->newId();
            $sData['id'] = $newPageItemId;
            $sData['page_id'] = $newPageId;
            self::save($sData);

            // 远端保存页面项
            $prefix = config('database.prefix');
            $tableName = $prefix . 'universal_item_' . $item['item_key'];
            $tableClass = DbOperate::getService($tableName);
            if (class_exists($tableClass)) {
                $options = $item['optionArr'];
                $tableClass::downLoadRemoteConf($options, $newPageItemId);
            }
        }
        return true;
    }

    /**
     * 根据页面id筛选
     * @param type $pageId
     * @param type $subKey      动态表单子页面key
     * @return type
     */
    public static function selectByPageId($pageId, $subKey = '') {
        $data = UniformTableService::getByTableNo($subKey);
        // 20230331:用于字符替换
        $data['subKey'] = $subKey;

        $con[] = ['page_id', '=', $pageId];
        $con[] = ['status', '=', 1];

        $info = UniversalPageService::getInstance($pageId)->staticGet();
        if ($info['auth_check']) {
            // 20220825:带权限数据校验
            $lists = self::universalListWithAuth($con, false);
        } else {
            $lists = self::staticConList($con, '', 'sort');
        }

        Debug::debug('UniversalPageItemService的selectByPageId', $lists);
        //$lists = self::lists($con, 'sort','id,item_key,span,data_url,param,merge_param,show_condition,value,height,class,title,title_class');
        foreach ($lists as &$v) {
            // 20230331
            $v['data_url'] = Strings::dataReplace($v['data_url'], $data);
            $v['title'] = Strings::dataReplace($v['title'], $data);
            // 2022-12-17
            $hideKeys = DbOperate::keysForHide(['page_id', 'sort', 'status']);
            $v = Arrays::hideKeys($v, $hideKeys);

            $classStr = UniversalItemService::getClassStr($v['item_key']);
            Debug::debug('$classStr', $classStr);
            //配置选项
            $v['optionArr'] = class_exists($classStr) ? $classStr::optionArr($v['id'], $subKey) : [];
            //参数:20220430尝试返回[],发现小程序订单详情报错
            // 20230825:开发PC站，再次尝试换回数组
            $v['param'] = json_decode($v['param'],true);
            $v['conf'] = $v['conf'] ? json_decode($v['conf'],true) : [] ;
            //显示条件
            $v['show_condition'] = json_decode($v['show_condition']);
            if ($v['item_key'] == 'form') {
                // 表单验证规则
                $v['formRules'] = UniversalItemFormRuleService::getRules($v['id']);
            }
            // 20230826：通用结构(兼容前端静态html用)
            $v['itemStruct'] = UniversalStructureService::getItemStructure($v['id']);
        }
        return $lists;
    }

    /**
     * 钩子-保存前
     */
    public static function extraPreSave(&$data, $uuid) {
        if (isset($data['roleIds'])) {
            self::getInstance($uuid)->roleUniversalSave($data['roleIds']);
        }
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
        if (isset($data['roleIds'])) {
            self::getInstance($uuid)->roleUniversalSave($data['roleIds']);
        }
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
        $this->universalRoleClear();
    }

    /**
     *
     */
    public function fId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 页面id
     */
    public function fPageId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 项目key
     */
    public function fItemKey() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 排序
     */
    public function fSort() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 状态(0禁用,1启用)
     */
    public function fStatus() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 有使用(0否,1是)
     */
    public function fHasUsed() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 锁定（0：未锁，1：已锁）
     */
    public function fIsLock() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 锁定（0：未删，1：已删）
     */
    public function fIsDelete() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 备注
     */
    public function fRemark() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 创建者，user表
     */
    public function fCreater() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 更新者，user表
     */
    public function fUpdater() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 创建时间
     */
    public function fCreateTime() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 更新时间
     */
    public function fUpdateTime() {
        return $this->getFFieldValue(__FUNCTION__);
    }

}
