<?php

namespace xjryanse\universal\service;

use xjryanse\system\interfaces\MainModelInterface;
use think\facade\Request;
/**
 * 20220817页面访问日志
 */
class UniversalPageLogService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\RedisModelTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\universal\\model\\UniversalPageLog';
    //直接执行后续触发动作
    protected static $directAfter = true;

    /**
     * 日志记录
     * @param type $pageId
     * @return type
     */
    public static function log($pageId){
        $data['page_id']    = $pageId;
        $data['ip']         = Request::ip();
        return self::redisLog($data);
        // return self::save($data);
    }
    /**
     * 清除过期
     */
    public static function clearExpire($days = 30){
        $con[] = ['create_time','<=',date('Y-m-d H:i:s',strtotime('-'.$days.' days'))];
        return self::where($con)->delete();
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

    public function fCompanyId() {
        return $this->getFFieldValue(__FUNCTION__);
    }
    /**
     * 页面key
     */
    public function fPageId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 创建者，user表
     */
    public function fCreater() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 创建时间
     */
    public function fCreateTime() {
        return $this->getFFieldValue(__FUNCTION__);
    }

}
