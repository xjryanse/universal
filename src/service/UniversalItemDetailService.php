<?php

namespace xjryanse\universal\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\system\service\SystemColumnListService;
use xjryanse\logic\Strings;
use think\facade\Request;
/**
 * 列表
 */
class UniversalItemDetailService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

// 静态模型：配置式数据表
    use \xjryanse\traits\StaticModelTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\universal\\model\\UniversalItemDetail';

    /**
     * 必有方法
     */
    public static function optionArr($pageItemId) {
        $con[] = ['page_item_id', '=', $pageItemId];
        $con[] = ['status', '=', 1];
        $res = self::staticConList($con, '', 'sort');
        //$res = self::lists( $con ,'sort','id,label,show_label,field,type,option,show_condition,class,span,layer_url');
        foreach ($res as &$v) {
            $v = self::addOpt($v);
        }
        return $res;
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
        foreach ($res as &$v) {
            $v = self::addOpt($v);
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
        if (in_array($v['type'], ['array', 'form'])) {
            //子表单
            $v['subItems'] = self::subOptionArr($v['id']);
        }
        // 20230315：增加通用表单框
        if($v['type'] == 'common'){
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
    public function fIconPic() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * [顺2]宫格图标
     */
    public function fGridIcon() {
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
