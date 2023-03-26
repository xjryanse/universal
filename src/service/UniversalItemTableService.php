<?php

namespace xjryanse\universal\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\user\service\UserAuthRoleUniversalService;
use xjryanse\system\service\SystemColumnListService;
use xjryanse\logic\Arrays;
use xjryanse\logic\Strings;
use xjryanse\logic\Debug;
use xjryanse\logic\Arrays2d;
use xjryanse\logic\DbOperate;

/**
 * 列表
 */
class UniversalItemTableService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

// 静态模型：配置式数据表
    use \xjryanse\traits\StaticModelTrait;
    use \xjryanse\universal\traits\UniversalTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\universal\\model\\UniversalItemTable';
    //直接执行后续触发动作
    protected static $directAfter = true;  

    /**
     * 选项转化
     */
    protected static function itemDataCov(&$v) {
        // 枚举；动态枚举
        $v['option'] = Strings::isJson($v['option']) ? json_decode($v['option']) : $v['option'];
        if (in_array($v['type'], ['enum', 'dynenum'])) {
            $v['option'] = SystemColumnListService::getOption($v['type'], $v['option']);
        }
        if (in_array($v['type'], ['image'])) {
            $v['option'] = json_decode($v['option']);
        }
        $v['show_condition']    = json_decode($v['show_condition']);
        //弹窗参数
        $v['pop_param'] = json_decode($v['pop_param']);
        //其他配置
        $v['conf'] = json_decode($v['conf']) ?: null;
    }

    /**
     * 必有方法
     * 一对多
     */
    public static function optionArr($pageItemId) {
        $con[] = ['page_item_id', '=', $pageItemId];
        $con[] = ['status', '=', 1];
        // $info = UniversalPageItemService::mainModel()->where('id', $pageItemId)->find();
        $info = UniversalPageItemService::getInstance($pageItemId)->staticGet();

        if ($info['auth_check']) {
            // 20220825:带权限数据校验
            $resRaw = self::universalListWithAuth($con, true);
        } else {
            $resRaw = self::staticConList($con, '', 'sort');
        }
        
        // 2022-12-17
        $hideKeys   = DbOperate::keysForHide(['page_id','page_item_id','sort','status']);
        $res1       = Arrays2d::hideKeys($resRaw, $hideKeys);

        //$res = self::lists( $con );
        // 处理MONTH_DAY等特殊字串
        $res = self::listDeal($res1);
        
        foreach ($res as &$v) {
            self::itemDataCov($v);
            if (in_array($v['type'], ['button'])) {
                $v['option'] = UniversalItemBtnService::optionArr($pageItemId);
            }
            if (in_array($v['type'], ['multi'])) {
                $v['option'] = self::subOptionArr($v['id']);
            }
        }

        return $res;
    }
    /**
     * 2022-11-18:处理MONTH_DAY；YEAR_MONTH 等特殊字串；
     */
    public static function listDeal($uList){
        foreach ($uList as $k => &$v) {
            //每月的天数
            if ($v['name'] == 'MONTH_DAY') {
                // $data[] = ['tab_key' => '', 'tab_title' => '全部'];
                $rawPP = $v['pop_param'];
                for ($i = 1; $i <= 31; $i++) {
                    $v['label']     = $i.'日';
                    $v['name']      = 'd'.$i;
                    // 20221116 将MONTH_DAY替换为具体日期
                    $v['pop_param'] = str_replace('MONTH_DAY', str_pad($i, 2, "0", STR_PAD_LEFT), $rawPP);
                    $data[]         = $v;
                }

                array_splice($uList, $k, 1, $data);
            }
            //每年的月数
            if ($v['name'] == 'YEAR_MONTH') {
                // $data[] = ['tab_key' => '', 'tab_title' => '全部'];
                $rawPP = $v['pop_param'];
                for ($i = 1; $i <= 12; $i++) {
                    $v['label']     = $i.'月';
                    $v['name']      = 'm'.$i;
                    // 20221116 将MONTH_DAY替换为具体日期
                    $v['pop_param'] = str_replace('YEAR_MONTH', str_pad($i, 2, "0", STR_PAD_LEFT), $rawPP);
                    $data[]         = $v;
                }

                array_splice($uList, $k, 1, $data);
            }
        }
        
        return $uList;
    }

    public static function subOptionArr($subItemId) {
        $con[] = ['subitem_id', '=', $subItemId];
        $con[] = ['status', '=', 1];
        $res = self::staticConList($con, '', 'sort');
        Debug::debug('UniversalItemTableService 的 $con', $con);
        Debug::debug('UniversalItemTableService 的 subOptionArr', $res);

        //$res = self::lists( $con );
        foreach ($res as &$v) {
            self::itemDataCov($v);
            if (in_array($v['type'], ['button'])) {
                $v['option'] = UniversalItemBtnService::subOptionArr($v['id']);
            }
        }

        return $res;
    }

    /**
     * 获取导出字段明细
     * @param type $pageItemId
     */
    public static function exportFieldArr($pageItemId) {
        $con[] = ['page_item_id', '=', $pageItemId];
        $con[] = ['is_export', '=', 1];
        $res = self::mainModel()->where($con)->order('sort,id')->select();
        foreach ($res as &$v) {
            self::itemDataCov($v);
        }
        return $res ? $res->toArray() : [];
    }

    /**
     * 导出数据处理类库
     * @param type $fields      字段数组：本表查询结果二维数组
     * @param type $data        原始数据：二维数组
     * @param type $dynDataList 动态数据(用于替换)：键值对象，列表查询返回的dynDataList
     * @param type $sumFields   求和字段，一维数组
     * @param type $withTitle   是否带表头
     * @return type
     */
    public static function exportDataDeal($fields, $data, $dynDataList, $sumData = [], $withTitle = false) {
        //①表头
        $titleArr = [];
        if ($withTitle) {
            $tData = [];
            foreach ($fields as $field) {
                $tData[] = $field['label'];
            }
            $titleArr[] = $tData;
        }
        //②内容
        $resArr = [];
        foreach ($data as &$v) {
            $tmpArr = [];
            foreach ($fields as $field) {
                $fieldName = $field['name'];
                $value = $v[$fieldName];
                //枚举
                if ($field['type'] == 'enum') {
                    foreach ($field['option'] as &$opt) {
                        if ($opt['cate_key'] == $v[$fieldName]) {
                            $value = $opt['cate_name'];
                        }
                    }
                }
                //动态枚举
                if ($field['type'] == 'dynenum') {
                    $value = Arrays::value($dynDataList[$fieldName], $value) ?: $value;
                }
                //20220808日期时间
                if ($field['type'] == 'datetime') {
                    $value = $field['option'] ? date($field['option'], strtotime($value)) : $value;
                }
                //流程
                if ($field['type'] == 'flow') {
                    $tVal = Arrays::value($value, 'node_name');
                    $value = Arrays::value($value, 'flow_status') == 'todo' ? '等待' . $tVal : $tVal;
                }
                $tmpArr[] = $value;
            }
            $resArr[] = $tmpArr;
        }
        //③求和
        if ($sumData) {
            $sumArr = [];
            foreach ($fields as $fieldT) {
                if (!$sumArr) {
                    // 第一栏填合计
                    $sumArr[] = '合计';
                } else {
                    $sumArr[] = Arrays::value($sumData, $fieldT['name']);
                }
            }
            $resArr[] = $sumArr;
        }

        return array_merge($titleArr, $resArr);
    }

    /**
     * 用于导出标题头
     * @param type $pageItemId
     * @return type
     */
    public static function columnNameLabel($pageItemId) {
        $con[] = ['page_item_id', '=', $pageItemId];
        return self::mainModel()->where($con)->column('label', 'name');
    }

    /**
     * 传一堆字段数组保存
     * @param type $pageItemId
     * @param type $fields
     */
    public static function saveField($pageItemId, $fields) {
        self::checkTransaction();
        $dataArr = [];
        foreach ($fields as &$field) {
            $tmp = [];
            $tmp['page_item_id'] = $pageItemId;
            $tmp['label'] = $field;
            $tmp['name'] = $field;
            $tmp['type'] = 'text';
            $dataArr[] = $tmp;
        }
        $tmp = [];
        $tmp['page_item_id'] = $pageItemId;
        $tmp['label'] = '操作';
        $tmp['name'] = '';
        $tmp['type'] = 'button';
        $dataArr[] = $tmp;
        $res = self::saveAll($dataArr);
        return $res;
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

    public static function extraDetails($ids) {
        return self::commExtraDetails($ids, function($lists) use ($ids) {
            $universalTable = self::getTable();
            foreach ($lists as &$v) {
                $v['roleIds'] = UserAuthRoleUniversalService::universalRoleIds($universalTable, $v['id']);
                $v['roleCount']      = count($v['roleIds']);
            }
            return $lists;
        });
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
