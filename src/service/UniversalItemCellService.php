<?php

namespace xjryanse\universal\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 列表
 */
class UniversalItemCellService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\universal\\model\\UniversalItemCell';

    /**
     * 必有方法
     * 一对一
     */
    public static function optionArr($pageItemId) {
        $con[] = ['page_item_id', '=', $pageItemId];
        $con[] = ['status', '=', 1];
        /*         * 卡片组* */
        $res = self::lists($con);
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
     *
     */
    public function fId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * [冗]页面id
     */
    public function fPageId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * page_item表的id
     */
    public function fPageItemId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * [顺1]图标
     */
    public function fIcon() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * [顺2]图标颜色
     */
    public function fIconColor() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 宫格跳转地址
     */
    public function fUrl() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    public function fClass() {
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
