<?php

namespace xjryanse\universal\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\system\service\SystemColumnListService;
use xjryanse\system\service\SystemConfigsService;
use xjryanse\system\service\columnlist\Dynenum;
use xjryanse\system\logic\ConfigLogic;
use xjryanse\logic\ModelQueryCon;
use xjryanse\logic\Strings;
use xjryanse\logic\Arrays;
use xjryanse\logic\Debug;
use xjryanse\logic\Url;
use xjryanse\logic\Arrays2d;
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

// 静态模型：配置式数据表
    use \xjryanse\traits\StaticModelTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\universal\\model\\UniversalItemForm';
    /**
     * 2022-12-18：已转成数组的options配置
     * @var type 
     */
    protected $options = [];
    /**
     * 必有方法
     */
    public static function optionArr($pageItemId) {
        $con[] = ['page_item_id', '=', $pageItemId];
        $con[] = ['status', '=', 1];
        $resRaw = self::staticConList($con, '', 'sort');
        //$res = self::lists( $con ,'sort','id,label,show_label,field,default_val,type,disabled,multi,is_must,option,update_url,show_condition,class,span,layer_url');
        // 2022-12-17
        $hideKeys = DbOperate::keysForHide(['page_id', 'page_item_id', 'sort', 'status']);
        $res = Arrays2d::hideKeys($resRaw, $hideKeys);
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
        }

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

        $resRaw = self::staticConList($con, 'sort');
        //2022-12-17
        $hideKeys = DbOperate::keysForHide(['page_id', 'page_item_id', 'subitem_id', 'sort', 'status']);
        $res = Arrays2d::hideKeys($resRaw, $hideKeys);

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
        $v['option'] = Strings::dataReplace($v['option'], $data);
        if (in_array($v['type'], ['enum', 'dynenum', 'radio', 'check', 'tplset', 'dyntree', 'dynradio'])) {
            if (in_array($v['type'], ['radio'])) {
                // radio为枚举
                $v['option'] = SystemColumnListService::getOption('enum', $v['option']);
            } else if (in_array($v['type'], ['dynenum', 'dynradio'])) {
                $v['option'] = SystemColumnListService::getOption('dynenum', $v['option']);
            } else {
                $v['option'] = SystemColumnListService::getOption($v['type'], $v['option']);
            }
        } else {
            $v['option'] = Strings::isJson($v['option']) ? json_decode($v['option']) : $v['option'];
        }
        if (in_array($v['type'], ['array', 'form', 'dynArr'])) {
            //子表单
            $v['subItems'] = self::subOptionArr($v['id']);
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
    /**
     * 获取数据配置，已解析为数组
     * @param type $key
     * @param type $defaultVal
     * @return type
     */
    public function getOptionVal($key,$defaultVal = ''){
        if(!$this->options){
            // 2022-12-20 TODO：可能涉及远端数据
            $columnInfo             = $this->get() ? : $this->remoteGet();
            $this->options          = equalsToKeyValue($columnInfo['option']);
        }
        return Arrays::value($this->options, $key, $defaultVal);
    }
    /**
     * 2022-12-20：跨系统远端配置
     * @return boolean
     * @throws Exception
     */
    public function remoteGet(){
        $baseHost   = ConfigLogic::config('systemBaseHost');
        if(!$baseHost){
            return false;
        }

        $url            = $baseHost.'/'.session(SESSION_COMPANY_KEY).'/webapi/Universal/formGet';
        $param['id']    = $this->uuid;
        $finalUrl       = Url::addParam($url, $param);

        $res        = Query::get($finalUrl);
        if($res['code'] == 0){
            return $res['data'];
        } else {
            throw new Exception('基本站异常：'.$res['message']);
        }
    }
    
    /**
     * 动态搜索
     * @param type $search          搜索字段
     * @param type $uniFieldData    联动字段值
     * @return type
     * @throws Exception
     */
    public function dynSearch($search = '', $uniFieldData = '', $param = [], $struct = ''){
        //20220815有自定义class优先：
        $className  = $this->getOptionVal( 'class');
        //自定义类的方法名
        $methodName = $this->getOptionVal('method');
        // ******* 普通的业务逻辑 **********
        // 
        $tableField     = $this->getOptionVal('value');
        $tableName      = $this->getOptionVal('table_name');
        //联动写入字段
        //一般为id
        $fieldId        = $this->getOptionVal('key','id');        
        //20220629排序字段
        $orderBy        = $this->getOptionVal('orderBy','sort desc');
        
        //20220914:写给目标数据的字段名
        $uniSetField    = $this->getOptionVal('uniSetField');
        //20220914：从当前表提取的字段名:默认跟目标数据字段名一致
        $uniValField    = $this->getOptionVal('uniValField') ? :$uniSetField;
        //【1】自定义类优先
        if($className && $methodName){
            $lists = self::dynSearchByMethod($className, $methodName, $param);
        } else {
            if(!$tableName){
                throw new Exception('动态枚举数据源异常，请联系开发'.$this->uuid);
            }
            // 组装查询条件
            $con = $this->getDynSearchCon($search, $uniFieldData);
            // 组装查询字段
            $resFields      = [$fieldId, $tableField,$uniValField];
            $resFieldArr    = Arrays::unsetEmpty($resFields);
            // 返回查询列表
            $lists = self::dynSearchByDb($search, $tableName, $fieldId, $resFieldArr, $orderBy, $con);
        }

        return $this->dynSearchDeal($lists, $struct);
    }
    /**
     * 组装查询条件
     * @param type $search
     * @param type $uniData
     * @return int
     */
    protected function getDynSearchCon($search, $uniData = []){
        $tableField     = $this->getOptionVal( 'value');
        //联动查询字段
        $uniFieldName   = $this->getOptionVal( 'uni_field','');
        $tableName      = $this->getOptionVal( 'table_name');

        $conStr         = $this->getOptionVal('con');
        if($conStr){
            $conParam           = json_decode($conStr,JSON_UNESCAPED_UNICODE);
            $fields['equal']    = array_keys($conParam);
            $con = ModelQueryCon::queryCon($conParam, $fields);
        }

        if($search ){
            $con[] = [ $tableField ,'like' ,'%'.$search.'%' ];
        }
        if($uniFieldName && Arrays::value($uniData, $uniFieldName) && !$search){
            $con[] = [ $uniFieldName ,'=' ,Arrays::value($uniData, $uniFieldName)];
        }
        $service = DbOperate::getService($tableName);
        if($service::mainModel()->hasField('company_id')){
            $con[] = ['company_id','=',session(SESSION_COMPANY_ID)];
        }
        //20220513:剔除被删记录
        if($service::mainModel()->hasField('is_delete')){
            $con[] = ['is_delete','=',0];
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
    protected static function dynSearchByMethod($className, $methodName, $param = []){
        if(method_exists($className, $methodName)){
            // $list = call_user_func([ $className , $methodName],Request::param());
            $list = call_user_func([ $className , $methodName],$param);
            return $list;
        } else {
            throw new Exception('配置异常，请联系您的软件服务商');
        }
    }
    /**
     * 类库查询
     */
    protected static function dynSearchByService(){
        
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
    protected static function dynSearchByDb($search, $tableName,$fieldId,array $fieldArr, $orderBy, $con = []){
        $limit      = 20;
        $service    = DbOperate::getService($tableName);
        // 2022-12-18：静态查询
        if(method_exists($service, 'staticConList')){
            $listsRaw   = $service::staticConList($con,'',$orderBy);
            $lists      = array_slice($listsRaw, 0, $limit);
            if(!$lists){
                // 2022-12-19
                $cone       = [['id','=',$search]];
                $lists      = $service::staticConList($cone);
            }
        } else {
            // 20220523，增加了过滤条件
            $inst = Db::table($tableName)->where( $con );
            if($search ){
                //20220119，通过id搜索
                $inst = $inst->whereOr('id',$search);
            }
            //数据库查询的字段数组
            $fieldQStr = implode(',',$fieldArr);
            //20220814:5秒缓存
            $lists = $inst->limit($limit)->group($fieldId)->order($orderBy)->cache(5)->field($fieldQStr)->select();
        }
        return $lists;
    }
    /**
     * 返回结果组装
     * @return type
     */
    protected function dynSearchDeal($lists, $structRaw ){
        $struct     = $structRaw ? : $this->getOptionVal( 'struct');

        $tableField     = $this->getOptionVal( 'value');
        //联动写入字段
        //一般为id
        $fieldId        = $this->getOptionVal('key','id');    
        //20220914:写给目标数据的字段名
        $uniSetField    = $this->getOptionVal('uniSetField');
        //20220914：从当前表提取的字段名:默认跟目标数据字段名一致
        $uniValField    = $this->getOptionVal( 'uniValField') ? :$uniSetField;
        
        // 2022-12-24:逐步过渡到json配置
        $covFieldArr = Strings::isJson($uniValField) ? json_decode($uniValField,JSON_UNESCAPED_UNICODE) : [$uniValField=>$uniSetField];
        if($struct == 'select2'){
            return Dynenum::dataResArr2d($lists, $fieldId, $tableField, $covFieldArr);
        } else {
            return Dynenum::dataResArr($lists, $fieldId, $tableField);
        }
    }
}
