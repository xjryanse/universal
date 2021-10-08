<?php

namespace xjryanse\universal\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\Cachex;

/**
 * 页面表
 */
class UniversalPageService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\universal\\model\\UniversalPage';

    public static function getByPageKey($pageKey) {
        $con[] = ['page_key', '=', $pageKey];
        return self::find($con);
    }

    public function getCache(){
        return  Cachex::funcGet('UniversalPageService_getCache'.$this->uuid, function(){
            return $this->get();
        });
    }
    /**
     * 获取页面配置
     * @return type
     */
    public function getPage() {
        $res = self::mainModel()->where('id', $this->uuid)->field('id,group_id,page_key,page_name,api_url')->find();
        if ($res) {
            $res['pageItems'] = UniversalPageItemService::selectByPageId($this->uuid);
            $res['pageGroup'] = UniversalGroupService::mainModel()->where('id',$res['group_id'])->field('id,group_name,need_manage,fault_route,fault_describe')->find();
        }

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
