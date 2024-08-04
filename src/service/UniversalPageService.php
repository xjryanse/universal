<?php

namespace xjryanse\universal\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\uniform\service\UniformTableService;
use xjryanse\system\service\SystemAbilityPageKeyService;
use xjryanse\wechat\service\WechatWeAppQrSceneService;
use xjryanse\universal\service\UniversalPageItemService;
use xjryanse\universal\service\UniversalItemService;
use xjryanse\system\service\SystemCompanyService;
use xjryanse\system\service\SystemHostService;
use xjryanse\system\logic\TableLogic;
use xjryanse\logic\BaseSystem;
use xjryanse\logic\Arrays;
use xjryanse\logic\Arrays2d;
use xjryanse\logic\Debug;
use xjryanse\logic\Strings;
use xjryanse\logic\DbOperate;
use think\facade\Cache;
use think\facade\Request;
use Exception;

/**
 * 页面，规则定义如下
 * p    -后台页面
 * ps   -后台统计页面
 * m    -微信小程序页面
 * w    -公众号页面
 */
class UniversalPageService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    // 静态模型：配置式数据表
    use \xjryanse\traits\StaticModelTrait;
    use \xjryanse\traits\ObjectAttrTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\universal\\model\\UniversalPage';

    use \xjryanse\universal\service\page\FieldTraits;
    use \xjryanse\universal\service\page\DefaultPageTraits;
    use \xjryanse\universal\service\page\TriggerTraits;
    use \xjryanse\universal\service\page\DoTraits;
    use \xjryanse\universal\service\page\CalTraits;
    use \xjryanse\universal\service\page\FrPageTraits;
    
    public static function extraDetails( $ids ){
        return self::commExtraDetails($ids, function($lists) use ($ids){
            //子项数
            // $itemCountArr   = UniversalPageItemService::groupBatchCount('page_id', $ids);
            //浏览次数
            $logCountArr    = UniversalPageLogService::groupBatchCount('page_id', $ids);

            foreach($lists as &$v){
                // 子项数
                // $v['itemCount'] = Arrays::value($itemCountArr, $v['id']);
                // 浏览记录数
                $v['logCount']  = Arrays::value($logCountArr, $v['id']);
            }
            return $lists;
        },true);
    }
    
    public static function getByPageKey($pageKey) {
        // 20230607
        if(!$pageKey){
            return [];
        }
        // 20230619:优化性能
        if(!self::$staticListsAll){
            self::staticListsAll();
        }
        return array_filter(self::$staticListsAll, function ($var) use ($pageKey) {
            return ($var['page_key'] == $pageKey);
        });
    }
    /**
     * 20230602:用于后台修改，get取数据
     * @param type $param
     */
    public static function findByKeyOrId($param=[]){
        $keyOrId = Arrays::value($param, 'id');
        if(!$keyOrId){
            // 20240107:提取当前编辑的页面
            $keyOrId = Cache::get('currentEditPageKeyOrId');
        }
        $id = $keyOrId;
        if(!Strings::isSnowId($keyOrId)){
            $id = self::keyToId($keyOrId);
        }
        Cache::set('currentEditPageKeyOrId', $id);
        return self::getInstance($id)->info();
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
    
    /**
     * 20220524 复制页面
     */
    public function copyPage($targetPageKey = ''){
        $infoRaw = $this->get();
        if(!$infoRaw){
            throw new Exception('页面不存在');
        }
        if(self::keyToId($targetPageKey)){
            throw new Exception('目标页面'.$targetPageKey.'已存在');
        }
        //【1】保存页面
        $info               = is_array($infoRaw) ? $infoRaw : $infoRaw->toArray();
        $newPageId          = self::mainModel()->newId();
        $info['id']         = $newPageId;
        $info['page_key']   = $targetPageKey ? : $info['page_key'].'Copy';
        $res = self::save($info);
        //【2】页面项目
        $con[] = ['page_id','=',$this->uuid];
        $con[] = ['status','=',1];
        $itemsRaw = UniversalPageItemService::mainModel()->where( $con )->select();
        $items = $itemsRaw ? $itemsRaw->toArray() : [];
        foreach($items as &$v){
            UniversalPageItemService::getInstance($v['id'])->copyItem($newPageId);
        }
        
        return $res;
    }
    /**
     * 复制页面，但是替换字段
     * 表单，详情，表格的字段
     * @createTime 20230909
     * @param type $newPageKey      
     * @param type $fieldArr        ['label'=>'','field'=>'']
     * @param type $replaceArr      替换字段：['T26N010'=>'T26N011']
     * @return type
     * @throws Exception
     */
    public function copyPageReplaceField($newPageKey, $fieldArr = [], $replaceArr= []){
        $infoRaw = $this->get();
        if(!$infoRaw){
            throw new Exception('页面不存在'.$this->uuid);
        }
        if(self::getByPageKey($newPageKey)){
            throw new Exception('目标页已存在'.$newPageKey);
        }

        // 【1】复制页面
        $newPageId          = self::mainModel()->newId();
        // T26N010替换为 T26N011；
        $info               = Arrays::strReplace($infoRaw, $replaceArr);
        $info['id']         = $newPageId;
        $info['page_key']   = $newPageKey;
        
        $res = self::save($info);
        // 【2】复制页面项
        $con        = [];
        $con[]      = ['page_id','=',$this->uuid];
        $itemsRaw   = UniversalPageItemService::mainModel()->where( $con )->select();
        $items      = $itemsRaw ? $itemsRaw->toArray() : [];
        foreach($items as &$v){
            if(in_array($v['item_key'],['form','table','detail','list'])){
                UniversalPageItemService::getInstance($v['id'])->copyItemReplaceField($newPageId, $fieldArr, $replaceArr);
            } else {
                UniversalPageItemService::getInstance($v['id'])->copyItem($newPageId, $replaceArr);
            }
        }
        return $res;
    }
    
    /**
     * 20230331:下载远端的页面配置
     */
    public static function downLoadRemotePage($pageKey, $thisPageKey){
        self::checkTransaction();

        $con[] = ['page_key','=',$thisPageKey];
        $info = self::find($con);
        if($info){
            throw new Exception('本地页面'.$thisPageKey.'已存在');
        }
        $conf = self::getPageWithSys($pageKey);
        //保存page
        $sData              = $conf;
        $sData['page_key']  = $thisPageKey;
        // 新的页面id
        $newPageId          = self::mainModel()->newId();
        $sData['id']        = $newPageId;
        $res                = self::save($sData);
        //保存item
        //保存form、btn、……
        UniversalPageItemService::downLoadRemotePageItem($conf['id'], $newPageId);

        return $res;
    }

    /**
     * 获取页面配置
     * @param type $subKey  20230329:兼容万能表单
     * @return type
     */
    public function getPage($subKey = '') {
        $resRaw = $this->staticGet();
        $keysForHide    = DbOperate::keysForHide();
        $res            = Arrays::hideKeys($resRaw, $keysForHide);
        if(!$res){
            return [];
        }

        $res['page_key']    = $subKey ? $res['page_key'].'_'. $subKey : $res['page_key'];
        // 数据表名：用于前端快速定位
        $res['tableName']   = self::pageKeyTableName($res['page_key']);
        $res['pageItems']   = UniversalPageItemService::selectByPageId($this->uuid, $subKey);
        $res['pageGroup']   = UniversalGroupService::getInstance($res['group_id'])->getGroup();
        // 20231014:参数
        $res['param']       = $res['param'] ? json_decode($res['param'],true) : null ;

        // 20230331:用于字符替换
        $data               = UniformTableService::getByTableNo($subKey);
        Debug::debug('UniformTableService::getByTableNo($subKey)', $data);
        $data['subKey']     = $subKey;
        $res['page_name']   = Strings::dataReplace($res['page_name'], $data);
        $res['api_url']     = Strings::dataReplace($res['api_url'], $data);
        // 20230801:缓存key
        $res['cache_page_keys'] = explode(',', $res['cache_page_keys']);
        // 20231116:手机端管理页面路径，PC端管理页面路径
        $comKey                 = SystemHostService::hostMainComKey();
        $res['webManageUrl']    = Request::domain().'/wp/'.$comKey.'/wUniversalPageDetail/'.$res['id'];
        $res['pcManageUrl']     = Request::domain().'/manage/#/'.$comKey.'/universal/pUniversalPageEdit?id='.$res['id'];
        //20220817:增加访问日志记录
        UniversalPageLogService::log($res['id']);

        return $res;
    }
    /**
     * 20230324:提取页面配置
     * 无配置时，从远端提取
     */
    public static function getPageWithSys($pageKey, $cate = ''){
        // 万能表单，以_分隔
        $keysArr    = explode('_',$pageKey);
        $id         = self::convertId(Arrays::value($keysArr, 0), $cate);
        // 20230329:增加提取动态表单参数
        $page       = self::getInstance($id)->getPage(Arrays::value($keysArr, 1));
        if(!$page){
            $page   = self::sysPage($pageKey);
            // 20230331：是远端的配置
            $page['isRemote'] = 1;
        } else {
            // 20230331：不是远端的配置
            $page['isRemote'] = 0;
        }
        // 20230420:提取后，从本系统拼接一些配置数据
        $page = self::addThisSysData($page);
        // 20240411：方便调试
        if(Debug::isDevIp()){
            Cache::set('currentEditPageKeyOrId', $id);
        }
        
        return $page;
    }
    /**
     * 添加本系统数据
     * @param type $page
     */
    protected static function addThisSysData(&$page){
        if(isset($page['pageItems'])){
            foreach($page['pageItems'] as &$v){
                if($v['item_key'] == 'form'){
                    foreach($v['optionArr'] as &$vv){
                        $vv = UniversalItemFormService::addThisSysData($vv);
                        //dump($vv);
                    }
                }
            }
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
                throw new Exception(session(SESSION_COMPANY_ID).'页面id必须:'. $id);
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
        return BaseSystem::baseSysGet('/webapi/Universal/pageGet',$param);
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

    public static function tableNameGetWebBaseName( $tableName ){
        $tableArr   = explode('_',$tableName);
        array_shift ($tableArr);
        return Strings::camelize(implode('_',array_merge(['w_sl'],$tableArr)));
    }

    /**
     * 20230325
     * @return type
     */
    protected static function getExpFields(){
        return ['id','company_id','is_lock','has_used','is_delete','creater','updater','create_time','update_time'];
    }

    
    /**
     * 带项目的key保存
     * @param type $key     页面key
     * @param type $items   项目
     * @param type $table   表名
     * @return type
     */
    public static function saveWithItemGetPageId($key,$items, $table, $sData = []){
        self::checkTransaction();
        $sData['cate']      = 'admin'; 
        $sData['page_key']  = $key; 
        $sData['group_id']  = ''; 
        $res = self::save($sData);
        foreach($items as &$item){
            $tmp = [];
            $tmp['page_id'] = $res['id'];
            if(is_array($item)){
                //20230820:传数组
                $tmp = array_merge($tmp, $item);
            } else {
                // 原来的传key
                $tmp['item_key'] = $item;
                if($item == 'table'){
                    $controller = DbOperate::getController($table);
                    $tableKey   = DbOperate::getTableKey($table);
                    $tmp['data_url']    = '/admin/'.$controller.'/list?admKey='.$tableKey;
                    $tmp['conf']        = '{"border":1,"select":1}';
                }
                if($item == 'tab'){
                    $tmp['show_condition']  = '{"status":1}';
                    $tmp['value']           = 'valTab';
                }
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
            if($pageItem['item_key'] == 'detail'){
                UniversalItemDetailService::saveField($pageItem['id'],$fields);
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
     * 提取当前类型下的字段名称
     * @describe 用于get数据处理
     * @createTime 2023-07-18 19:29:00
     * @creater xjryanse
     * @param type $type 类型：比如：dynenum,uplimage
     */
    public function typeFields($subKey = '', $type = 'text'){
        // 提取全部列表
        $con[]  = ['page_id','=',$this->uuid];
        $pageItemList = UniversalPageItemService::staticConList($con);

        $fields = [];
        foreach($pageItemList as $v){
            $tFields = [];
            if($v['item_key'] == 'form'){
                $tFields = UniversalItemFormService::typeFields($v['id'], $subKey, $type );
            }
            if($v['item_key'] == 'detail'){
                $tFields = UniversalItemDetailService::typeFields($v['id'], $subKey, $type );
            }

            $fields = array_merge($fields, $tFields);
        }
        
        //步骤1：
        return $fields;
    }

    /**
     * [单条]数据处理，例如（图片，动态枚举等）
     * @param type $data
     */
    public static function dataInfoDealAttr($data, $uPageId, $tableNo) {
        // 20230717:提取字段明细
        
        
        $picFields = UniversalItemDetailService::getFieldsByPageItemId($uPageId, $tableNo);
        /* 图片字段提取 */
        if ($picFields) {
            $data = Arrays2d::picFieldCov($data, $picFields);
        }
        return $data;
    }
    /**
     * 20230726:公司是否有某页面的权限
     * @param type $companyId
     * @param type $pageKey
     */
    public static function compHasAuth($pageKey){
        $lists = SystemAbilityPageKeyService::allPageList();
        Debug::debug('compHasAuth的$lists',$lists);
        $pages = $lists ? array_unique(array_column($lists, 'page_key')) : [];
        Debug::debug('compHasAuth的$pages',$pages);
        // 列表之外默认有权
        if(!in_array($pageKey, $pages)){
            return true;
        }
        $con[] = ['hasAuth','=',1];
        $hasAuthList = Arrays2d::listFilter($lists, $con);
        $hasAuthPages = $hasAuthList ? array_unique(array_column($hasAuthList, 'page_key')) : [];
        return in_array($pageKey, $hasAuthPages);
    }
    /**
     * 20230906:标准字段
     */
    public function standardFields(){
        $con[] = ['page_id','=',$this->uuid];
        $con[] = ['item_key','in',['table','form','detail']];
        $pageItems = UniversalPageItemService::staticConList($con);
        
        $arr = [];
        foreach($pageItems as $v){
            $tmp = [];
            $tmp['page_item_id']    = $v['id'];
            $tmp['itemType']        = $v['item_key'];
            // 匹配子类
            $classStr = UniversalItemService::getClassStr($v['item_key']);
            // 标准化字段
            $standardFields = class_exists($classStr) && method_exists($classStr, 'standardFields') ? $classStr::standardFields($v['id']) : [];
            $arr = array_merge($arr,$standardFields);
        }

        return $arr;
    }
    /**
     * 生成小程序场景值二维码
     * @createTime 2023-09-08
     */
    public function generateWeAppQrScene(){
        $info = $this->get();
        if(Arrays::value($info, 'cate') != 'weApp'){
            throw new Exception('不是小程序页面'.$this->uuid);
        }
        $data = [];
        $data['pageId'] = Arrays::value($info, 'page_key');
        $fromTable = self::getTable();
        return WechatWeAppQrSceneService::generate($data, $fromTable, $this->uuid);
    }
    /**
     * 20230914:同步页面下的字段名称
     */
    public function syncFieldName(){
        // 提取页面名称：
        $tableName = $this->fBaseTable();
        if(!$tableName){
            $pageKey = $this->fPageKey();
            throw new Exception('页面' . $pageKey . '未配置表名');
        }

        // 提取页面字段：TODO;
        $tableLogicInst = new TableLogic($tableName);
        $tableFields    = $tableLogicInst->fieldsArr();
        
        $pageItems      = UniversalPageItemService::dimListByPageId($this->uuid);
        foreach($pageItems as $v){
            $classStr   = UniversalItemService::getClassStr($v['item_key']);
            // 同步字段
            if(method_exists($classStr, 'coverFieldByPageItemId')){
                $pageItemId = $v['id'];
                //TODO提取表的字段
                $classStr::coverFieldByPageItemId($pageItemId,$tableFields);
            }
        }

        return true;
    }
    
    /*
     * 删除整个页面：含各明细
     */
    public function delRecur(){
        // 【1】提取页面项
        $pageItems      = UniversalPageItemService::dimListByPageId($this->uuid);
        // 【2】删页面项
        foreach($pageItems as $v){
            $classStr   = UniversalItemService::getClassStr($v['item_key']);
            // 【2-1】删明细
            if(method_exists($classStr, 'delRecur')){
                $pageItemId = $v['id'];
                //TODO提取表的字段
                $classStr::delRecur($pageItemId);
            }
            // 【2-2】删页面项
            UniversalPageItemService::getInstance($v['id'])->deleteRam();
        }
        // 【3】删页面
        $this->deleteRam();
        return true;
    }
    /**
     * 提取当前web链接，用于跳链
     */
    public static function currentWebUrl($pageKey, $comKey = ''){
        if(!$comKey){
            $companyId  = session(SESSION_COMPANY_ID);
            $comKey     = SystemCompanyService::getInstance($companyId)->fKey();
        }
        $url = Request::domain(true).'/wp/'.$comKey.'/'.$pageKey;
        return $url;
    }

}
