<?php

namespace xjryanse\universal\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 页面表
 */
class UniversalStructureService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    // 静态模型：配置式数据表
    use \xjryanse\traits\StaticModelTrait;
    use \xjryanse\traits\TreeTrait;
    use \xjryanse\universal\traits\UniversalTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\universal\\model\\UniversalStructure';
    //直接执行后续触发动作
    protected static $directAfter = true;  
    
    /**
     * 20220822:酷酷酷酷酷酷
     * @param type $pageItemId
     * @return type
     */
    public static function getItemStructure($pageItemId){
        $con[] = ['page_item_id','=',$pageItemId];
        $con[] = ['status','=',1];
        $lists = self::staticConList($con,'','sort');
        foreach($lists as &$v){
            $v['option'] = self::universalOptionCov($v['field_type'], $v['option']);
            $v['element_class']     = json_decode($v['element_class'],JSON_UNESCAPED_UNICODE) ?: $v['element_class'];
            $v['show_condition']    = json_decode($v['show_condition'],JSON_UNESCAPED_UNICODE);
        }
        //拼接树状
        $res = self::makeTree($lists, '');

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
     *
     */
    public function fId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    public function fGroupId() {
        return $this->getFFieldValue(__FUNCTION__);
    }
    /**
     * 页面key
     */
    public function fPageKey() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 页面名称
     */
    public function fPageName() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * api接口路径：有配置拿配置；没配置取默认
     */
    public function fApiUrl() {
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
