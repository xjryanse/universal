<?php

namespace xjryanse\universal\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\user\service\UserAuthRoleUniversalService;
use xjryanse\logic\Debug;
use xjryanse\logic\DbOperate;
use xjryanse\logic\Arrays;
use xjryanse\logic\Arrays2d;
use think\Db;
/**
 * 万能表单页面项目
 */
class UniversalPageItemService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    // 静态模型：配置式数据表
    use \xjryanse\traits\StaticModelTrait;
    // 带权限查询
    use \xjryanse\universal\traits\UniversalTrait;
    
    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\universal\\model\\UniversalPageItem';
    //直接执行后续触发动作
    protected static $directAfter = true;  
    
    public static function extraDetails($ids) {
        return self::commExtraDetails($ids, function($lists) use ($ids) {
            $universalTable = self::getTable();
            foreach ($lists as &$v) {
                $v['roleIds']       = UserAuthRoleUniversalService::universalRoleIds($universalTable, $v['id']);
                $v['roleCount']     = count($v['roleIds']);
            }
            return $lists;
        });
    }
    /**
     * 20220524
     * 复制单条项目
     */
    public function copyItem($newPageId){
        $infoRaw = $this->get();
        if(!$infoRaw){
            throw new Exception('页面项'.$this->uuid.'不存在');
        }
        //【1】保存页面
        $info      = is_array($infoRaw) ? $infoRaw : $infoRaw->toArray();
        $newPageItemId = self::mainModel()->newId();
        $info['id']         = $newPageItemId;        
        $info['page_id']    = $newPageId;
        $res                = self::save($info);
        // 页面项
        $prefix     = config('database.prefix');
        $tableName  = $prefix.'universal_item_'.$info['item_key'];
        $tableClass = DbOperate::getService($tableName);
        if(class_exists($tableClass) && DbOperate::hasField($tableName, 'page_item_id')){
            //【2】页面项目
            $con[] = ['page_item_id','=',$this->uuid];
            $items = Db::table($tableName)->where($con)->select();
            foreach($items as &$v){
                $v['id']            = self::mainModel()->newId();
                $v['page_id']       = $newPageId;
                $v['page_item_id']  = $newPageItemId;
            }
            $tableClass::saveAll($items);
        }
        
        return $res;
    }
    /**
     * 根据页面id筛选
     * @param type $pageId
     * @return type
     */
    public static function selectByPageId($pageId) {
        $con[] = ['page_id', '=', $pageId];
        $con[] = ['status', '=', 1];
        
        $info = UniversalPageService::getInstance($pageId)->staticGet();
        if($info['auth_check']){
            // 20220825:带权限数据校验
            $lists = self::universalListWithAuth($con, false);
        } else {
            $lists = self::staticConList($con, '', 'sort');            
        }
        
        Debug::debug('UniversalPageItemService的selectByPageId',$lists);
        //$lists = self::lists($con, 'sort','id,item_key,span,data_url,param,merge_param,show_condition,value,height,class,title,title_class');
        foreach ($lists as &$v) {
            // 2022-12-17
            $hideKeys   = DbOperate::keysForHide(['page_id','sort','status']);
            $v          = Arrays::hideKeys($v, $hideKeys);

            $classStr   = UniversalItemService::getClassStr($v['item_key']);
            Debug::debug('$classStr', $classStr);
            //配置选项
            $v['optionArr'] = class_exists($classStr) ? $classStr::optionArr($v['id']) : [];
            //参数:20220430尝试返回[],发现小程序订单详情报错
            $v['param']     = json_decode($v['param']);
            $v['conf']      = json_decode($v['conf']) ? : new \stdClass();
            //显示条件
            $v['show_condition']     = json_decode($v['show_condition']);
            if($v['item_key'] == 'form'){
                // 表单验证规则
                $v['formRules'] = UniversalItemFormRuleService::getRules($v['id']);
            }
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
