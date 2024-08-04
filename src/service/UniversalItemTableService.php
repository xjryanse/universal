<?php

namespace xjryanse\universal\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\user\service\UserAuthRoleUniversalService;
use xjryanse\system\service\SystemColumnListService;
use xjryanse\uniform\service\UniformTableService;
use xjryanse\uniform\service\UniformUniversalItemTableService;
use xjryanse\logic\Arrays;
use xjryanse\logic\Strings;
use xjryanse\logic\Debug;
use xjryanse\logic\Arrays2d;
use xjryanse\logic\DbOperate;
use xjryanse\system\service\columnlist\Dynenum;
use think\facade\Request;
use Exception;

/**
 * 列表
 */
class UniversalItemTableService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;


// 静态模型：配置式数据表
    use \xjryanse\traits\StaticModelTrait;
    use \xjryanse\universal\traits\UniversalTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\universal\\model\\UniversalItemTable';
    //直接执行后续触发动作
    protected static $directAfter = true;
    // 20231008
    protected static $itemKey = 'table';
    
    use \xjryanse\universal\service\itemTable\DimTraits;
    use \xjryanse\universal\service\itemTable\DataDealTraits;
    
    /**
     * 选项转化
     */
    protected static function itemDataCov(&$v) {
        // 枚举；动态枚举
        $v['option'] = $v['option'] && Strings::isJson($v['option']) ? json_decode($v['option']) : $v['option'];
        // 20240511
        if (in_array($v['type'], ['enum', 'dynenum'])) {
            $v['option'] = SystemColumnListService::getOption($v['type'], $v['option']);
        }
        $v['show_condition'] = json_decode($v['show_condition']);
        //弹窗参数
        $v['pop_param'] = json_decode($v['pop_param']);
        //其他配置
        $v['conf'] = json_decode($v['conf']) ?: null;
        // 20240326
        $v['update_param'] = json_decode($v['update_param']) ?: null;
    }

    /**
     * 必有方法
     * 一对多
     */
    public static function optionArr($pageItemId, $subKey = '') {
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
        $hideKeys = DbOperate::keysForHide(['page_id', 'page_item_id', 'sort', 'status']);
        $res1 = Arrays2d::hideKeys($resRaw, $hideKeys);
        //$res = self::lists( $con );
        // 处理MONTH_DAY等特殊字串
        $res = self::listDeal($res1, $subKey);

        foreach ($res as &$v) {
            self::itemDataCov($v);
            if (in_array($v['type'], ['button'])) {
                $v['option'] = UniversalItemBtnService::optionArr($pageItemId, $subKey);
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
    public static function listDeal($uList, $subKey = '') {
        
        foreach ($uList as $k => &$v) {
            // 20230715:增
            $tData['comKey'] = session(SESSION_COMPANY_KEY);
            $tData['domain'] = Request::domain(true);
            $v['option']    = Strings::dataReplace($v['option'], $tData);

            //每月的天数
            if ($v['name'] == 'MONTH_DAY') {
                // $data[] = ['tab_key' => '', 'tab_title' => '全部'];
                $rawPP = $v['pop_param'];
                for ($i = 1; $i <= 31; $i++) {
                    $v['label'] = $i . '日';
                    $v['name'] = 'd' . $i;
                    // 20221116 将MONTH_DAY替换为具体日期
                    $v['pop_param'] = str_replace('MONTH_DAY', str_pad($i, 2, "0", STR_PAD_LEFT), $rawPP);
                    $data[] = $v;
                }

                array_splice($uList, $k, 1, $data);
            }
            //每年的月数
            if ($v['name'] == 'YEAR_MONTH') {
                // $data[] = ['tab_key' => '', 'tab_title' => '全部'];
                $rawPP = $v['pop_param'];
                for ($i = 1; $i <= 12; $i++) {
                    $v['label'] = $i . '月';
                    $v['name'] = 'm' . $i;
                    // 20221116 将MONTH_DAY替换为具体日期
                    $v['pop_param'] = str_replace('YEAR_MONTH', str_pad($i, 2, "0", STR_PAD_LEFT), $rawPP);
                    $data[] = $v;
                }

                array_splice($uList, $k, 1, $data);
            }
            // 20230329:万能联动表单
            if ($v['type'] == 'uniform') {
                $tableId = UniformTableService::tableToId($subKey);
                $data = UniformUniversalItemTableService::getOptionArr($tableId, $v['name']);
                // $data[] = ['tab_key' => '', 'tab_title' => '全部'];
//                $v['label']     = '性别';
//                $v['name']      = 'sex';
//                $v['type']      = 'text';
//                $data[]         = $v;

                array_splice($uList, $k, 1, $data);
            }
            // 20240604:子级表
            $v['subItems'] = self::subOptionArr($v['id']);
            
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
            // 20240703
            $v['subItems'] = self::subOptionArr($v['id']);
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
                $tmpArr[$fieldName] = $value;
            }
            $resArr[] = $tmpArr;
        }
        //③求和
        if ($sumData) {
            $sumArr = [];
            foreach ($fields as $fieldT) {
                if (!$sumArr) {
                    // 第一栏填合计
                    $sumArr[$fieldT['name']] = '合计';
                } else {
                    $sumArr[$fieldT['name']] = Arrays::value($sumData, $fieldT['name']);
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
    public static function saveField($pageItemId, $fields, $tableName = '') {
        self::checkTransaction();
        
        $controller = DbOperate::getController($tableName);
        $tableKey   = DbOperate::getTableKey($tableName);
        
        $dataArr = [];
        foreach ($fields as &$field) {
            // 20240320
            if(in_array($field,['sort','source'])){
                continue;
            }

            $tmp = [];
            $tmp['page_item_id'] = $pageItemId;
            $tmp['name'] = $field;
            if($field == 'status'){
                $tmp['label']       = '状态';
                $tmp['type']        = 'switch';
                $tmp['update_url']  = '/admin/'.$controller.'/updateRam?admKey='.$tableKey;
                $tmp['width']       = 50;
            } else {
                $tmp['label']   = $field;
                $tmp['type']    = 'text';
            }
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
     * 20230331
     * @param type $pageItemId
     * @param type $newPageItemId
     * @return boolean
     */
    public static function downLoadRemoteConf($pageItemId, $newPageItemId) {        
        // 表格字段
        self::universalSysItemsDownload($pageItemId, $newPageItemId);
        // 按钮
        UniversalItemBtnService::universalSysItemsDownload($pageItemId, $newPageItemId);
        return true;
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
                        $v['roleCount'] = count($v['roleIds']);
                    }
                    return $lists;
                });
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
        // dump($res);exit;
        return $res;
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
                $arr[] = $v['name'];
            }
        }
        return $arr;
    }

    /**
     * 20240707:用于提取子列表
     * @param type $subItemId
     */
    protected static function subList($subItemId) {
        $con[]  = ['subitem_id', '=', $subItemId];
        $con[]  = ['status', '=', 1];
        $lists  =  self::staticConList($con, '', 'sort');
        // Debug::dump($con);
        $arr    = $lists;
        foreach($lists as $v){
            $tmpArr = self::subList($v['id']);
            if($tmpArr){
                $arr = array_merge($arr,$tmpArr);
            }
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
        //20240707
        $subList = [];
        foreach($lists as $v){
            $tmpList = self::subList($v['id']);
            if($tmpList){
                $subList = array_merge($subList, $tmpList);
            }
        }
        if($subList){
            $lists = array_merge($lists, $subList);
        }
        
        if (!$lists) {
            // 系统远端提取
            // $lists = self::sysItems($pageItemId);
            $lists = self::universalSysItems($pageItemId);
        }
        $cone[] = ['status', '=', 1];
        $cone[] = ['type', '=', 'dynenum'];
        $listEE = Arrays2d::listFilter($lists, $cone);
        // 20240411:防刁民
        foreach($listEE as $v){
            if(!$v['option']){
                throw new Exception('未配置动态枚举参数'.$pageItemId.'-'.$v['name']);
            }
        }
        
        return array_column($listEE, 'option', 'name');
    }

//    /**
//     * 远端提取项目列表
//     * @return type
//     */
//    protected static function sysItems($pageItemId) {
//        $param['pageItemId'] = $pageItemId;
//        return BaseSystem::baseSysGet('/webapi/Universal/tableList', $param);
//    }

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
            $tmp    = ['page_item_id'=>$pageItemId,'itemType'=>'table','label'=>$v['label'],'field'=>$v['name'],'type'=>$v['type']];
            $arr[]  = $tmp;
        }

        return $arr;
    }
    
    /*
     * 标准字段转本表字段
     * @param type $pageItemId
     * @param type $fieldsArr
     * @param type $withOperate     是否带上操作按钮
     * @return type
     */
    public static function standardFieldToThis($pageItemId, $fieldsArr = []){
        $arr = [];
        foreach($fieldsArr as $v){
            $tmp = $v;
            $tmp['page_item_id']    = $pageItemId;
            $tmp['name']            = Arrays::value($v, 'field');

            $arr[] = $tmp;
        }

        return $arr;
    }
    /**
     * 
     */
    public static function copyKeepFields($rawPageItemId, $newPageItemId, $keepFields = ['status','audit_status','audit_reason']){
        $con[] = ['page_item_id','=',$rawPageItemId];
        $con[] = ['name','in',$keepFields];
        $fields = self::where($con)->select();
        $fieldsArr = $fields ? $fields->toArray() : [];
        // 替换page_item_id
        foreach($fieldsArr as &$v){
            $v['page_item_id']    = $newPageItemId;
        }
        
        //带上操作按钮
        $fieldsArr[] = [
            'page_item_id'  =>$newPageItemId
            ,'label'        =>'操作'
            ,'type'         =>'button'
        ];
        
        return $fieldsArr;
    }

    /**
     * 20230914：按页面项，覆盖字段信息
     * @param type $pageItemId
     */
    public static function coverFieldByPageItemId($pageItemId, $newFields = []){
        // 数据表中的字段名
        $fieldM = 'name';
        // 提取页面项字段
        $fieldArr = self::dimListByPageItemId($pageItemId);
        // 标签的键值对：field=>label
        $labelObj = Arrays2d::toKeyValue($newFields, 'field', 'label');
        $objArr     = Arrays2d::fieldSetKey($newFields, 'field');
        // 循环，替换，更新
        foreach($fieldArr as $v){
            if($v[$fieldM] && Arrays::value($objArr, $v[$fieldM])){
                $upData             = [];
                $upData['label']    = Arrays::value($objArr[$v[$fieldM]], 'label');
                $upData['type']     = Arrays::value($objArr[$v[$fieldM]], 'type');
                $upData['option']   = Arrays::value($objArr[$v[$fieldM]], 'option');
                self::getInstance($v['id'])->updateRam($upData);
            }
        }
        return true;
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
        // 20230914：表格的比较特殊，还要删一下按钮
        UniversalItemBtnService::delRecur($pageItemId);

        return true;
    }
    /**
     * 页面项字段，用于查询过滤
     */
    public static function pageItemFieldsForDataFilter($pageItemId){
        $fields     = self::dimFieldsByPageItemId($pageItemId);
        $fields[]   = 'id';
        $fields[]   = 'company_id';
        $fields[]   = 'status';
        
        return array_unique($fields);
    }
    
    /**
     * 20231105:表格数据过滤
     */
    public static function dataArrFilter($pageItemId, $dataArr){
        if(Debug::isDevIp()){
            // 20231105方便调试
            return $dataArr;
        }

        if(!UniversalPageItemService::getInstance($pageItemId)->fFieldFilter()){
            return $dataArr;
        }
        
        $fieldArr = self::pageItemFieldsForDataFilter($pageItemId);
        if($fieldArr){
            $dataArr = Arrays2d::getByKeys($dataArr,$fieldArr);
        }

        return $dataArr;
    }
    
    
    /**
     * 获取求和字段明细
     * @param type $pageItemId
     */
    public static function sumFieldArr($pageItemId) {
        $con[] = ['page_item_id', '=', $pageItemId];
        $con[] = ['is_sum', '=', 1];
        $res = self::mainModel()->where($con)->field('label,name')->order('sort,id')->select();
        return $res ? $res->toArray() : [];
    }
    
    public static function sumFields($pageItemId){
        $con[] = ['page_item_id', '=', $pageItemId];
        $con[] = ['is_sum', '=', 1];
        $names = self::mainModel()->where($con)->column('name');
        // 计算求和的字段
        $calcParams = self::calcInnParams($pageItemId);
        
        return array_unique(array_merge($names, $calcParams));
    }
    
    /**
     * 获取计算字段明细
     * 20231221
     * @param type $pageItemId
     */
    public static function calcFieldArr($pageItemId) {
        $con[] = ['page_item_id', '=', $pageItemId];
        $con[] = ['is_calc', '=', 1];
        $res = self::mainModel()->where($con)
                ->field('label,name,is_calc,calc_method')
                ->order('sort,id')
                ->cache(1)
                ->select();
        return $res ? $res->toArray() : [];
    }
    /**
     * 计算属性涉及的参数
     */
    public static function calcInnParams($pageItemId){
        $arr = self::calcFieldArr($pageItemId);
        $params = [];
        foreach($arr as $v){
            $tmp    = Strings::getParams($v['calc_method']);
            $params = array_merge($params, $tmp);
        }
        return array_unique($params);
    }
    
}
