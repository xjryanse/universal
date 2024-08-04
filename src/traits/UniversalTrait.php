<?php
namespace xjryanse\universal\traits;

use xjryanse\user\service\UserAuthRoleUniversalService;
use xjryanse\system\service\SystemColumnListService;
use xjryanse\user\service\UserService;
use xjryanse\logic\Strings;
use xjryanse\logic\Debug;
use xjryanse\logic\Arrays;
use xjryanse\logic\BaseSystem;

/**
 * 需要依赖
    use \xjryanse\traits\StaticModelTrait;
 */
trait UniversalTrait {
    /**
     * 带权限的列表查询
     * @param type $con
     * @param type $emptyAll    0830权限查无时，是否全部提取？
     * @return type
     */
    protected static function universalListWithAuth($con = [],$emptyAll = true){
        // 20231231：增加超管全权限
        $userId     = session(SESSION_USER_ID);
        $userInfo   = UserService::getInstance($userId)->get();
        $admType = Arrays::value($userInfo,'admin_type');
        // 超管全权限
        if( $admType == 'super' || $admType == 'subSuper'){
            $res = self::staticConList($con, '', 'sort');   
            return $res;
        }
        
        $res = [];        
        $tableName = self::getTable();        
        $universalIds = UserAuthRoleUniversalService::userUniversalIds($tableName);
        Debug::debug('universalListWithAuth的$tableName',$tableName);
        Debug::debug('universalListWithAuth的$universalIds',$universalIds);
//        dump($tableName);
//        dump($universalIds);
        //带权限的查询
        if($universalIds ){
            $cone = $con;
            $cone[] = ['id','in',$universalIds];
            $res = self::staticConList($cone, '', 'sort');
        }
        //权限查询无结果，全部查
        if(!$res && $emptyAll){
            $res = self::staticConList($con, '', 'sort');   
        }
        return $res;
    }
    /**
     * 
     */
    protected function roleUniversalSave($roleIds){
        $universalTable = self::mainModel()->getTable();
        $res = UserAuthRoleUniversalService::universalRoleIdSave($universalTable,$this->uuid,$roleIds);
        return $res;
    }
    /**
     * 2022-12-16
     */
    protected function universalRoleClear(){
        $universalTable = self::mainModel()->getTable();
        UserAuthRoleUniversalService::universalClear($universalTable, $this->uuid);
    }
    
    /**
     * 20220922 通用选项转换
     */
    protected static function universalOptionCov($type, $optionRaw){
        $option = '';
        if(in_array($type, ['enum','dynenum','radio','tplset'])){
            if($type == 'radio'){
                // radio为枚举
                $option = SystemColumnListService::getOption('enum', $optionRaw);
            } else {
                $option = SystemColumnListService::getOption($type, $optionRaw);
            }
        } else {
            $option = Strings::isJson($optionRaw) ? json_decode($optionRaw) : $optionRaw;
        }
        return $option;
    }
    
    
    /**
     * 远端提取项目列表
     * @return type
     */
    protected static function universalSysItems($pageItemId) {
        $param['pageItemId']    = $pageItemId;
        $param['itemKey']       = self::$itemKey;
        return BaseSystem::baseSysGet('/webapi/Universal/commPageItemSubList', $param);
    }
    
    
    /*
     * 下载基本站页面子项目数据，替换pageItemId
     * @createTime 2023-10-08
     */
    protected static function universalSysItemsDownload($pageItemId, $newPageItemId){
        $optionsN    = self::universalSysItems($pageItemId);
        $sArr = [];
        foreach ($optionsN as $item) {
            $sData = $item;
            $newItemId = self::mainModel()->newId();
            $sData['id'] = $newItemId;
            $sData['page_item_id'] = $newPageItemId;
            $sArr[] = $sData;
        }
        return self::saveAllRam($sArr);
    }
}
