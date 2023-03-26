<?php

namespace xjryanse\universal\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\system\logic\ConfigLogic;
use xjryanse\logic\Arrays;
use xjryanse\logic\Strings;
use xjryanse\logic\Debug;
use xjryanse\logic\DbOperate;
use xjryanse\curl\Query;
use think\facade\Cache;
use Exception;

/**
 * 页面表
 */
class UniversalPageService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    // 静态模型：配置式数据表
    use \xjryanse\traits\StaticModelTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\universal\\model\\UniversalPage';

    public static function extraDetails( $ids ){
        return self::commExtraDetails($ids, function($lists) use ($ids){
            //子项数
            $itemCountArr   = UniversalPageItemService::groupBatchCount('page_id', $ids);
            //浏览次数
            $logCountArr    = UniversalPageLogService::groupBatchCount('page_id', $ids);

            foreach($lists as &$v){
                // 子项数
                $v['itemCount'] = Arrays::value($itemCountArr, $v['id']);
                // 浏览记录数
                $v['logCount']  = Arrays::value($logCountArr, $v['id']);
            }
            return $lists;
        });
    }
    
    public static function getByPageKey($pageKey) {
        $con[] = ['page_key', '=', $pageKey];
        return self::staticConFind($con);  
        //return self::find($con);
    }
    /**
     * 页面key或页面id提取id
     * @param type $keyOrId
     * @return type
     */
    public static function strGetId($keyOrId){
        $con[] = ['page_key','=',$keyOrId];
        $info = self::staticConFind($con);  
        if(!$info){
            $info = self::getInstance($keyOrId)->get();
        }
        return $info ? $info['id'] : "";
    }
    /**
     * 20220717
     */
    public static function keyToId($key){
        $con[]  = ['page_key','=',$key];
        $info   = self::staticConFind($con);  
        return $info ? $info['id'] : '';
    }
    
    public function getCache(){
        return $this->get();
//        return  Cachex::funcGet('UniversalPageService_getCache'.$this->uuid, function(){
//            return $this->get();
//        });
    }
    /**
     * 20220524 复制页面
     */
    public function copyPage(){
        $infoRaw = $this->get();
        if(!$infoRaw){
            throw new Exception('页面不存在');
        }
        //【1】保存页面
        $info      = is_array($infoRaw) ? $infoRaw : $infoRaw->toArray();
        $newPageId = self::mainModel()->newId();
        $info['id'] = $newPageId;
        $info['page_key'] = $info['page_key'].'Copy';
        $res = self::save($info);
        //【2】页面项目
        $con[] = ['page_id','=',$this->uuid];
        $itemsRaw = UniversalPageItemService::mainModel()->where( $con )->select();
        $items = $itemsRaw ? $itemsRaw->toArray() : [];
        foreach($items as &$v){
            UniversalPageItemService::getInstance($v['id'])->copyItem($newPageId);
        }
        
        return $res;
    }    
    /**
     * 获取页面配置
     * @return type
     */
    public function getPage() {
        $resRaw = $this->staticGet();
        $keys = ['id','group_id','bind_company_id','page_key','page_name','api_url','param_get'];
        $res = $resRaw ? Arrays::getByKeys($resRaw, $keys) : [];
//        $res = self::mainModel()->where('id', $this->uuid)->field('id,group_id,bind_company_id,page_key,page_name,api_url')->find();
        if ($res) {
            // 数据表名：用于前端快速定位
            $res['tableName'] = self::pageKeyTableName($res['page_key']);
            $res['pageItems'] = UniversalPageItemService::selectByPageId($this->uuid);
            $res['pageGroup'] = UniversalGroupService::getInstance($res['group_id'])->getGroup();
            //20220817:增加访问日志记录
            UniversalPageLogService::log($res['id']);
        }
        return $res;
    }
    /**
     * 20230324:提取页面配置
     * 无配置时，从远端提取
     */
    public static function getPageWithSys($pageKey, $cate = ''){
        $id     = self::convertId($pageKey, $cate);
        $page   = self::getInstance($id)->getPage();
        if(!$page){
            $page = self::sysPage($pageKey);
        }
        return $page;
    }
    /*
     * 转换id
     */
    protected static function convertId($id, $cate = ''){
        //【1】无值取默认
        if(!$id){
            $id = UniversalCompanyDefaultPageService::getDefaultKey(session(SESSION_COMPANY_ID), $cate);
            if(!$id){
                throw new Exception(session(SESSION_COMPANY_ID).'页面id必须', $id);
            }
        }
        //【2】非id转id
        // 20220717：判断非雪花id，则转换一下
        if(!Strings::isSnowId($id)){
            $id = self::keyToId($id);
        }
        return $id;
    }
    
    /**
     * 20230325:系统远端的页面配置
     * @param type $id
     * @return boolean
     * @throws Exception
     */
    protected static function sysPage( $pageKey){
        $param['id']    = $pageKey;
        return self::baseSysGet('/webapi/Universal/pageGet',$param);
    }
    /**
     * 2022-12-29:页面key，反定位表名：
     * 开发模式用
     */
    public static function pageKeyTableName($pageKey){
        $keyStr = Strings::uncamelize($pageKey);
        $keyArr = explode('_',$keyStr);
        array_shift($keyArr);
        array_pop($keyArr);
        return config('database.prefix').implode('_',$keyArr);
    }
    /**
     * 表名取页面基本名
     * @param type $tableName
     * @return type
     */
    public static function tableNameGetBaseName( $tableName ){
        $tableArr   = explode('_',$tableName);
        array_shift ($tableArr);
        return Strings::camelize(implode('_',array_merge(['p'],$tableArr)));
    }
    /**
     * 默认的列表页面
     * @param type $tableName
     * @return type
     */
    public static function defaultListKey($tableName){
        $baseName   = self::tableNameGetBaseName($tableName);
        return $baseName."List";
    }
    /**
     * 20230325:默认详情页
     * @param type $tableName
     * @return type
     */
    public static function defaultDetailKey($tableName){
        $baseName   = self::tableNameGetBaseName($tableName);
        return $baseName."Detail";
    }
    /**
     * 默认的添加页面
     * @param type $tableName
     * @return type
     */
    public static function defaultAddKey($tableName){
        $baseName   = self::tableNameGetBaseName($tableName);
        return $baseName."Add";
    }
    /**
     * 2023-01-15 月统计
     * @param type $tableName
     * @return type
     */
    public static function defaultMonthlyStaticsKey($tableName){
        $baseName   = self::tableNameGetBaseName($tableName);
        return $baseName."MonthlyStatics";
    }
    /**
     * 2023-01-15 年统计
     * @param type $tableName
     * @return type
     */
    public static function defaultYearlyStaticsKey($tableName){
        $baseName   = self::tableNameGetBaseName($tableName);
        return $baseName."YearlyStatics";
    }
    /**
     * 20230325
     * @return type
     */
    protected static function getExpFields(){
        return ['id','company_id','is_lock','has_used','is_delete','creater','updater','create_time','update_time'];
    }
    /**
     * 20220427保存列表页面
     */
    public static function saveListPage ($tableName) {
        $baseName   = self::tableNameGetBaseName($tableName);
        $fieldsArr  = DbOperate::columns($tableName);
        $expFields  = self::getExpFields();
        $fields     = array_diff(array_column($fieldsArr,'Field'),$expFields);
        $pageName   = $baseName."List";
        $items      = ['form','btn','table'];
        //带项目保存，获取页面id
        $pageId     = self::saveWithItemGetPageId($pageName, $items, $tableName);
        //【三】保存子项目
        $con[]      = ['page_id','=',$pageId];
        $pageItems  = UniversalPageItemService::lists($con);
        foreach($pageItems as $pageItem){
            if($pageItem['item_key'] == 'form'){
                UniversalItemFormService::saveField($pageItem['id'],$fields);
            }
            if($pageItem['item_key'] == 'btn'){
                $btnArr = [];
                $btnArr[] = ['name'=>'查询','cate'=>'paginate'      ,'size'=>'mini','icon'=>'el-icon-search','type'=>'success','data_url'=>'','trigger'=>''];
                $btnArr[] = ['name'=>'添加','cate'=>'layerUniversal','size'=>'mini','icon'=>'','type'=>'','data_url'=>$baseName.'Add','trigger'=>'list'];
                UniversalItemBtnService::saveBtn($pageItem['id'],$btnArr);
            }
            if($pageItem['item_key'] == 'table'){
                UniversalItemTableService::saveField($pageItem['id'],$fields);
            }
        }
        return $pageName;
    }
    /**
     * 保存添加/编辑页面
     */
    public static function saveAddPage($tableName){
        $baseName   = self::tableNameGetBaseName($tableName);
        $fieldsArr  = DbOperate::columns($tableName);
        // $fields     = array_column($fieldsArr,'Field');
        $expFields  = self::getExpFields();
        $fields     = array_diff(array_column($fieldsArr,'Field'),$expFields);
        // 添加页面
        $pageName   = $baseName."Add";
        $items      = ['form','btn'];
        //带项目保存，获取页面id
        $pageId     = self::saveWithItemGetPageId($pageName, $items, $tableName);
        //【三】保存子项目
        $con[]      = ['page_id','=',$pageId];
        $pageItems  = UniversalPageItemService::lists($con);
        foreach($pageItems as $pageItem){
            if($pageItem['item_key'] == 'form'){
                //20220605:24
                UniversalItemFormService::saveField($pageItem['id'],$fields,24);
            }
            if($pageItem['item_key'] == 'btn'){
                $tableArr   = explode('_',$tableName);
                $urlKey     = count($tableArr) > 2 ? Strings::camelize(implode('_',array_splice($tableArr,2))) : 'index'; 
                $btnArr     = [];
                $btnArr[]   = ['name'=>'保存','cate'=>'listOperate'      ,'size'=>'mini','data_url'=>'/admin/'.$tableArr[1].'/saveGetInfo?admKey='.$urlKey ,'trigger'=>'close'];
                UniversalItemBtnService::saveBtn($pageItem['id'],$btnArr);
            }
        }
        return $pageName;
    }

    /**
     * 保存添加/编辑页面
     */
    public static function saveDetailPage($tableName){
        $baseName   = self::tableNameGetBaseName($tableName);
        $fieldsArr  = DbOperate::columns($tableName);
        // $fields     = array_column($fieldsArr,'Field');
        $expFields  = self::getExpFields();
        $fields     = array_diff(array_column($fieldsArr,'Field'),$expFields);
        // 添加页面
        $pageName   = $baseName."Detail";
        $items      = ['detail'];
        //带项目保存，获取页面id
        $pageId     = self::saveWithItemGetPageId($pageName, $items, $tableName);
        //【三】保存子项目
        $con[]      = ['page_id','=',$pageId];
        $pageItems  = UniversalPageItemService::lists($con);
        foreach($pageItems as $pageItem){
            if($pageItem['item_key'] == 'detail'){
                //20220605:24
                UniversalItemDetailService::saveField($pageItem['id'],$fields,24);
            }
        }
        return $pageName;
    }
    /**
     * 带项目的key保存
     * @param type $key     页面key
     * @param type $items   项目
     * @param type $table   表名
     * @return type
     */
    public static function saveWithItemGetPageId($key,$items, $table){
        self::checkTransaction();
        $sData['cate']      = 'pcAdm'; 
        $sData['page_key']  = $key; 
        $sData['group_id']  = '5275373413077962753'; 
        $res = self::save($sData);
        foreach($items as &$item){
            $tmp = [];
            $tmp['page_id'] = $res['id'];
            $tmp['item_key'] = $item;
            if($item == 'table'){
                $controller = DbOperate::getController($table);
                $tableKey   = DbOperate::getTableKey($table);
                $tmp['data_url'] = '/admin/'.$controller.'/list?admKey='.$tableKey;
            }
            $dataArr[] = $tmp;
        }
        UniversalPageItemService::saveAll($dataArr);
        return $res['id'];
    }
    
    /*
     * 保存页面
     * @param type $key     字符串
     * @param type $items   一维数组
     * @param type $fields  一维数组
     */
    public static function savePage($key,$items,$fields){
        self::checkTransaction();
        // 保存页面
        $sData['group_id'] = '5275373413077962753'; 
        $sData['cate'] = 'pcAdm'; 
        $sData['page_key'] = $key; 
        $sData['group_id'] = '5275373413077962753'; 
        $res = self::save($sData);
        //保存页面项
        $dataArr = [];
        foreach($items as &$item){
            $tmp = [];
            $tmp['page_id'] = $res['id'];
            $tmp['item_key'] = $item;
            $dataArr[] = $tmp;
        }
        UniversalPageItemService::saveAll($dataArr);
        //【三】保存子项目
        $con[] = ['page_id','=',$res['id']];
        $pageItems = UniversalPageItemService::lists($con);
        foreach($pageItems as $pageItem){
            if($pageItem['item_key'] == 'form'){
                UniversalItemFormService::saveField($pageItem['id'],$fields);
            }
            if($pageItem['item_key'] == 'table'){
                UniversalItemTableService::saveField($pageItem['id'],$fields);
            }
        }
        return $res;
    }
    /**
     * 20221109：清除用户的页面缓存
     * 
     */
    public static function clearUserPageCache ($userId ) {
        $pages = self::staticConList();
        foreach($pages as $page){
            $key = 'UniversalPage'.$page['id'].$userId;
            Cache::rm($key); 
        }
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
        $con[] = ['page_id','=',$this->uuid];
        $count = UniversalPageItemService::mainModel()->where($con)->count();
        if($count){
            throw new Exception('请先删除'.$count.'个页面项');
        }
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
