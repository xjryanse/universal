<?php

namespace xjryanse\universal\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\system\service\SystemColumnListService;
use xjryanse\system\service\SystemConfigsService;
use xjryanse\system\service\columnlist\Dynenum;
use xjryanse\system\service\SystemAuthFilterService;
use xjryanse\uniform\service\UniformTableService;
use xjryanse\uniform\service\UniformUniversalItemFormService;
use xjryanse\sql\service\SqlService;
use xjryanse\system\logic\ConfigLogic;
use xjryanse\logic\ModelQueryCon;
use xjryanse\logic\BaseSystem;
use xjryanse\logic\Strings;
use xjryanse\logic\Arrays;
use xjryanse\logic\Arrays2d;
use xjryanse\logic\Debug;
use xjryanse\logic\Url;
use xjryanse\logic\DbOperate;
use xjryanse\curl\Query;
use think\Db;
use Exception;

/**
 * 列表
 */
class UniversalItemFormService extends Base implements MainModelInterface {

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
    protected static $mainModelClass = '\\xjryanse\\universal\\model\\UniversalItemForm';

    use \xjryanse\universal\service\itemForm\TriggerTraits;
    use \xjryanse\universal\service\itemForm\FieldTraits;
    use \xjryanse\universal\service\itemForm\DimTraits;
    use \xjryanse\universal\service\itemForm\FrPageTraits;
    // use \xjryanse\universal\service\itemForm\PaginateTraits;

    // 20231008
    protected static $itemKey = 'form';
    
    /**
     * 2022-12-18：已转成数组的options配置
     * @var type 
     */
    protected $options = [];

    public static function extraDetails($ids) {
        return self::commExtraDetails($ids, function($lists) use ($ids) {
            
                    // $universalTable = self::getTable();
                    foreach ($lists as &$v) {
                        $v['pageId']    = UniversalPageItemService::getInstance($v['page_item_id'])->fPageId();
                        // 20230730
                        $v['pageCate']  = UniversalPageService::getInstance($v['pageId'])->fCate();
                        // 20230603:页面id
                        // $v['roleIds']   = UserAuthRoleUniversalService::universalRoleIds($universalTable, $v['id']);
                        // $v['roleCount'] = count($v['roleIds']);
                    }
            
            
                    return $lists;
                },true);
    }
    
    /**
     * 必有方法
     */
    public static function optionArr($pageItemId, $subKey = '') {
        $con[] = ['page_item_id', '=', $pageItemId];
        $con[] = ['status', '=', 1];
        $resRaw = self::staticConList($con, '', 'sort');
        //$res = self::lists( $con ,'sort','id,label,show_label,field,default_val,type,disabled,multi,is_must,option,update_url,show_condition,class,span,layer_url');
        // 2022-12-17
        $hideKeys = DbOperate::keysForHide(['page_id', 'page_item_id', 'sort', 'status']);
        $res1 = Arrays2d::hideKeys($resRaw, $hideKeys);

        // 处理MONTH_DAY等特殊字串
        $res = self::listDeal($res1, $subKey);
        // 20230816:拼接表单后面的按钮
//        $conBtn     = [];
//        $conBtn[]   = ['page_item_id','=',$pageItemId];
//        $btns = UniversalItemBtnService::staticConList();
        $btns = UniversalItemBtnService::optionArr($pageItemId);
        //2022-12-18:针对配置进行处理
        foreach ($res as $k => &$v) {
            //系统配置
            if ($v['type'] == 'SYS_CONF') {
                // $cList = SystemConfigsService::lists();
                $cList = SystemConfigsService::mainModel()->group('key')->select();
                $data = [];
                foreach ($cList as $cV) {
                    $tmp = ['show_label' => 1, 'label' => $cV['desc'], 'field' => $cV['key'], 'type' => $cV['type']];
                    $data[] = $tmp;
                }
                array_splice($res, $k, 1, $data);
            }
            // 20230305：列表选择，增加页面结构配置
            if ($v['type'] == 'listSelect') {
                // 20230305:页面结构
                $v['itemStruct'] = UniversalStructureService::getItemStructure($pageItemId);
            }
            // 20230816表单后向按钮
            $v['afterBtns'] = $btns ? Arrays2d::listFilter($btns, [['subitem_id','=',$v['id']]]) : [];
        }

        foreach ($res as &$v) {
            $v = self::addOpt($v);
        }
        return $res;
    }

    /**
     * 20230329
     * @param type $uList
     * @param type $subKey
     * @return type
     */
    public static function listDeal($uList, $subKey = '') {
        foreach ($uList as $k => &$v) {
            // 20230329:万能联动表单
            if ($v['type'] == 'uniform') {
                $tableId = UniformTableService::tableToId($subKey);
                $data = UniformUniversalItemFormService::getOptionArr($tableId, $v['field']);
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

        $resRaw = self::staticConList($con,'', 'sort');
        //2022-12-17
        $hideKeys = DbOperate::keysForHide(['page_id', 'page_item_id', 'subitem_id', 'status']);
        $res = Arrays2d::hideKeys($resRaw, $hideKeys);

        foreach ($res as &$v) {
            $v = self::addOpt($v);
        }
        return $res;
    }

    protected static function addOpt($v) {
        //展示条件
        $v['show_condition'] = json_decode($v['show_condition']);
        //参数替换
        $data['comKey'] = session(SESSION_COMPANY_KEY);
        $v['option'] = Strings::dataReplace($v['option'], $data);
        if (in_array($v['type'], ['enum', 'dynenum', 'radio', 'tplset', 'dyntree', 'dynradio'])) {
            if (in_array($v['type'], ['radio'])) {
                // radio为枚举
                $v['option'] = SystemColumnListService::getOption('enum', $v['option']);
            } else if (in_array($v['type'], ['dynenum', 'dynradio'])) {
                $v['option'] = SystemColumnListService::getOption('dynenum', $v['option']);
            } else {
                $v['option'] = SystemColumnListService::getOption($v['type'], $v['option']);
            }
        } else if($v['type'] == 'selInput'){
            $cArr = SystemColumnListService::getOption('enum', $v['option']);
            $v['option'] = Arrays2d::fieldSetKey($cArr, 'cate_key');
        } else {
            $v['option'] = Strings::isJson($v['option']) ? json_decode($v['option']) : $v['option'];
        }
        if (in_array($v['type'], ['array', 'form', 'dynArr'])) {
            //子表单
            $v['subItems'] = self::subOptionArr($v['id']);
        }
        // 20240408:前端表格详情
        if (in_array($v['type'], ['arrayTable'])) {
            //子表单
            $v['subItems'] = UniversalItemTableService::subOptionArr($v['id']);
        }
        //其他配置
        $v['conf'] = json_decode($v['conf']) ?: null;

        return $v;
    }

    /**
     * 20230420：添加本系统数据（兼容远端配置）
     * @param type $v
     */
    public static function addThisSysData(&$v) {
        if (in_array($v['type'], ['check'])) {
            $v['option'] = SystemColumnListService::getOption($v['type'], $v['option']);
        }
        return $v;
    }

    /**
     * 传一堆字段数组保存
     * 逐步淘汰；
     * @param type $pageItemId
     * @param type $fields
     */
    public static function saveField($pageItemId, $fields, $span = 6) {
        self::checkTransaction();
        $dataArr = [];
        foreach ($fields as &$field) {
            // 20240320
            if(in_array($field,['sort','status','source',''])){
                continue;
            }
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
     * 传字段数组进行保存
     * @createTime 2023-09-09
     * @param type $pageItemId
     * @param type $fieldsArr
     */
    public static function saveFieldArr($pageItemId, $fieldsArr = []){
        self::checkTransaction();
        $max = 50;
        if(count($fieldsArr) > $max){
            throw new Exception('字段数量超过'.$max.'请分批生成');
        }
        
        $dataArr = [];
        foreach ($fieldsArr as &$field) {
            $field['page_item_id']  = $pageItemId;
            $field['label']         = Arrays::value($field, 'label') ? : Arrays::value($field, 'field');
            $field['type']          = Arrays::value($field, 'type') ? : 'text';
            $field['span']          = Arrays::value($field, 'span') ? : 24;
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
     * 获取数据配置，已解析为数组
     * @param type $key
     * @param type $defaultVal
     * @return type
     */
    public function getOptionVal($key, $defaultVal = '') {
        if (!$this->options) {
            // 2022-12-20 TODO：可能涉及远端数据
            $columnInfo = $this->get() ?: $this->remoteGet();
            $this->options = equalsToKeyValue($columnInfo['option']);
        }
        return Arrays::value($this->options, $key, $defaultVal);
    }

    /**
     * 2022-12-20：跨系统远端配置
     * @return boolean
     * @throws Exception
     */
    public function remoteGet() {
        $baseHost = ConfigLogic::config('systemBaseHost');
        if (!$baseHost) {
            return false;
        }

        $url = $baseHost . '/' . session(SESSION_COMPANY_KEY) . '/webapi/Universal/formGet';
        $param['id'] = $this->uuid;
        $finalUrl = Url::addParam($url, $param);

        $res = Query::get($finalUrl);
        if ($res['code'] == 0) {
            return $res['data'];
        } else {
            throw new Exception('基本站异常：' . $res['message']);
        }
    }

    /**
     * 动态搜索
     * @param type $search          搜索字段
     * @param type $uniFieldData    联动字段值
     * @return type
     * @throws Exception
     */
    public function dynSearch($search = '', $uniFieldData = '', $param = [], $struct = '') {
        //20220815有自定义class优先：
        $className = $this->getOptionVal('class');
        //自定义类的方法名
        $methodName = $this->getOptionVal('method');
        // ******* 普通的业务逻辑 **********
        // 
        $tableField = $this->getOptionVal('value');
        $tableName = $this->getOptionVal('table_name');
        //联动写入字段
        //一般为id
        $fieldId = $this->getOptionVal('key', 'id');
        //20220629排序字段
        $orderBy = $this->getOptionVal('orderBy', '');

        $limit = $this->getOptionVal('limit', 20);

        //20220914:写给目标数据的字段名
        $uniSetField = $this->getOptionVal('uniSetField');
        //20220914：从当前表提取的字段名:默认跟目标数据字段名一致
        $uniValField = $this->getOptionVal('uniValField') ?: $uniSetField;
        // 20240518：增加的json转换逻辑
        $uniValArr = Strings::isJson($uniValField) ? json_decode($uniValField, JSON_UNESCAPED_UNICODE) : [$uniValField => $uniSetField];

        //【1】自定义类优先
        if ($className && $methodName) {
            $lists = self::dynSearchByMethod($className, $methodName, $param);
        } else {
            if (!$tableName) {
                throw new Exception('动态枚举数据源异常，请联系开发' . $this->uuid);
            }
            // 组装查询条件
            $con = $this->getDynSearchCon($search, $uniFieldData);
            // 20240316:增加过滤key（用于控制一些权限）
            $filterKey      = $this->getOptionVal('filterKey');
            if($filterKey){
                $filterDataIds  = SystemAuthFilterService::calFilterIdData($filterKey);
                $con[]          = [$fieldId,'in',$filterDataIds];
            }
            Debug::debug('组装完成的查询条件', $con);
            // 组装查询字段
            // 20240518:当是数组时，取key，不是数组时，取原
            $resFields  = array_merge([$fieldId, $tableField], array_keys($uniValArr));
            // Debug::dump($resFields);
            $resFieldArr = Arrays::unsetEmpty($resFields);
            // 返回查询列表
            $lists = self::dynSearchByDb($search, $tableName, $fieldId, $resFieldArr, $orderBy, $con, $limit);
        }

        return $this->dynSearchDeal($lists, $struct);
    }

    /**
     * 组装查询条件
     * @param type $search
     * @param type $uniData
     * @return int
     */
    protected function getDynSearchCon($search, $uniData = []) {
        $tableField = $this->getOptionVal('value');
        //联动查询字段
        $uniFieldName = $this->getOptionVal('uni_field', '');
        $tableName = $this->getOptionVal('table_name');
        // 20240603
        $con = [];
        $conStr = $this->getOptionVal('con');
        if ($conStr) {
            $conParam = json_decode($conStr, JSON_UNESCAPED_UNICODE);
            $fields['equal'] = array_keys($conParam);
            $con = ModelQueryCon::queryCon($conParam, $fields);
        }

        if ($search && !is_array($search)) {
            $con[] = [$tableField, 'like', '%' . $search . '%'];
        }
        if ($uniFieldName && is_array($uniData) && !$search) {
            // $con[] = [ $uniFieldName ,'=' ,Arrays::value($uniData, $uniFieldName)];
            foreach ($uniData as $key => $value) {
                $con[] = [$key, '=', $value];
            }
        }
        // 数据表在？
        if(DbOperate::isTableExist($tableName)){
            $service = DbOperate::getService($tableName);
            if ($service::mainModel()->hasField('company_id')) {
                $con[] = ['company_id', '=', session(SESSION_COMPANY_ID)];
            }
            //20220513:剔除被删记录
            if ($service::mainModel()->hasField('is_delete')) {
                $con[] = ['is_delete', '=', 0];
            }
        }
        return $con;
    }

    /**
     * 2022-12-18：动态类配置
     * @param type $className
     * @param type $methodName
     * @param type $param
     * @return type
     * @throws Exception
     */
    protected static function dynSearchByMethod($className, $methodName, $param = []) {
        if (method_exists($className, $methodName)) {
            // $list = call_user_func([ $className , $methodName],Request::param());
            $list = call_user_func([$className, $methodName], $param);
            return $list;
        } else {
            throw new Exception('配置异常，请联系您的软件服务商'.$className.$methodName);
        }
    }

    /**
     * 类库查询
     */
    protected static function dynSearchByService() {
        
    }

    /**
     * 数据表查询
     * @param type $search      搜索关键词
     * @param type $tableName   数据表
     * @param type $fieldId     key字段
     * @param type $fieldArr    查询的字段，数组
     * @param type $orderBy     排序
     * @param type $con         额外条件（有联动条件在外部拼接后传入）
     */
    protected static function dynSearchByDb($search, $tableName, $fieldId, array $fieldArr, $orderBy, $con = [], $limit = 20) {
        $service = DbOperate::isTableExist($tableName) ? DbOperate::getService($tableName) : '';
        // 2022-12-18：静态查询
        if ( $service && method_exists($service, 'staticConList')) {
            $listsRaw = $service::staticConList($con, '', $orderBy);
            $lists = array_slice($listsRaw, 0, $limit);
            if (!$lists) {
                // 2022-12-19
                $cone = [['id', '=', $search]];
                $lists = $service::staticConList($cone);
            }
        } else {
            // 20240122：增加动态sql中提取数据
            if(!DbOperate::isTableExist($tableName)){
                // 数据表不存在，说明是动态表
                $sql        = SqlService::keyBaseSql($tableName);
                $tableName  = $sql.' as mainTable';
            }

            // 20220523，增加了过滤条件
            $inst = Db::table($tableName)->where($con);
            if ($search && !is_array($search)) {
                //20220119，通过id搜索
                $inst = $inst->whereOr($fieldId, $search);
            }
            // Debug::dump($tableName);
            //数据库查询的字段数组
            $fieldQStr = implode(',', $fieldArr);
            //20220814:5秒缓存
            $inst->limit($limit)->group($fieldId);
            if($orderBy){
                $inst->order($orderBy);
            }
            // Debug::dump($inst->field($fieldQStr)->buildSql());
            $lists = $inst->cache(5)->field($fieldQStr)->select();
        }

        return $lists;
    }

    /**
     * 返回结果组装
     * @return type
     */
    protected function dynSearchDeal($lists, $structRaw) {
        $struct = $structRaw ?: $this->getOptionVal('struct');

        $tableField = $this->getOptionVal('value');
        //联动写入字段
        //一般为id
        $fieldId = $this->getOptionVal('key', 'id');
        //20220914:写给目标数据的字段名
        $uniSetField = $this->getOptionVal('uniSetField');
        //20220914：从当前表提取的字段名:默认跟目标数据字段名一致
        $uniValField = $this->getOptionVal('uniValField') ?: $uniSetField;

        // 2022-12-24:逐步过渡到json配置
        $covFieldArr = Strings::isJson($uniValField) ? json_decode($uniValField, JSON_UNESCAPED_UNICODE) : [$uniValField => $uniSetField];
        if ($struct == 'select2') {
            return Dynenum::dataResArr2d($lists, $fieldId, $tableField, $covFieldArr);
        } else {
            return Dynenum::dataResArr($lists, $fieldId, $tableField);
        }
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
            $tmp    = ['page_item_id'=>$pageItemId,'itemType'=>'form','label'=>$v['label'],'field'=>$v['field'],'type'=>$v['type']];
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
     * 20230914：按页面项，覆盖字段信息
     * @param type $pageItemId
     */
    public static function coverFieldByPageItemId($pageItemId, $newFields = []){
        // 提取页面项字段
        $fieldArr = self::dimListByPageItemId($pageItemId);
        // 标签的键值对：field=>label
        // $labelObj   = Arrays2d::toKeyValue($newFields, 'field', 'label');
        $objArr     = Arrays2d::fieldSetKey($newFields, 'field');
        // 循环，替换，更新
        foreach($fieldArr as $v){
            if($v['field'] && Arrays::value($objArr, $v['field'])){
                $upData             = [];
                $upData['label']    = Arrays::value($objArr[$v['field']], 'label');
                $upData['type']     = Arrays::value($objArr[$v['field']], 'type');
                $upData['option']   = Arrays::value($objArr[$v['field']], 'option');
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
        return true;
    }
    /**
     * 
     * @param type $oPageItemId 原页面项
     * @param type $nPageItemId 新页面项
     * @param type $fieldArr    标准字段
     * @throws Exception
     */
    public static function copyPageItemReplaceField($oPageItemId, $nPageItemId, $fieldArr, $replaceArr = []){
        // 拿原来的取一个
        $con    = [];
        $con[]  = ['page_item_id','=',$oPageItemId];
        $con[]  = ['status','=',1];
        $info   = self::where($con)->find();

        $infoArr    = $info ? $info->toArray() : [];
        $keys       = ['id','creater','updater','create_time','update_time'];
        $infoN      = Arrays::unset($infoArr, $keys);

        //字段处理和保存
        $arr = [];
        foreach($fieldArr as &$v){
            // 20230910:增加替换字符串
            $v                  = Arrays::strReplace($v, $replaceArr);
            $v['id']            = self::mainModel()->newId();
            // $v['page_id']       = $newPageId;
            $v['page_item_id']  = $nPageItemId;
            
            if(!Arrays::value($v, 'base_class')){
                // 默认在一行
                $v['base_class'] = 'flex bg-white';
            }
            
            $arr[] = array_merge($infoN, $v);
        }

        return self::saveAll($arr);
    }
    /**
     * 动态字段
     * 20230920
     * @param type $fieldsArr       只需要label;field,type
     * @return int
     */
    public static function dynArrFields($fieldsArr = []){
        foreach($fieldsArr as &$v){
            $v['id']            = self::mainModel()->newId();
            $v['show_label']    = isset($v['show_label']) ? $v['show_label'] : 1 ;
            $v['base_class']    = $v['type'] == 'selInput' ? 'bg-white' : 'flex bg-white';
            // $v['is_must']       = 1;
        }
        return $fieldsArr;
    }

}
