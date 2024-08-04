<?php

namespace xjryanse\universal\service\page;

use xjryanse\universal\service\UniversalPageItemService;
use xjryanse\universal\service\UniversalItemFormService;
use xjryanse\universal\service\UniversalItemBtnService;
use xjryanse\universal\service\UniversalItemTableService;
use xjryanse\universal\service\UniversalItemDetailService;
use xjryanse\logic\Strings;
use xjryanse\logic\DbOperate;

/**
 * 
 */
trait DefaultPageTraits{

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
     * 默认的列表页面
     * @param type $tableName
     * @return type
     */
    public static function defaultWebListKey($tableName){
        $baseName   = self::tableNameGetWebBaseName($tableName);
        return $baseName."List";
    }
    /**
     * 20230325:默认详情页
     * @param type $tableName
     * @return type
     */
    public static function defaultWebDetailKey($tableName){
        $baseName   = self::tableNameGetWebBaseName($tableName);
        return $baseName."Info";
    }
    /**
     * 默认的添加页面
     * @param type $tableName
     * @return type
     */
    public static function defaultWebAddKey($tableName){
        $baseName   = self::tableNameGetWebBaseName($tableName);
        return $baseName."Add";
    }
    
    
        /**
     * 20220427保存列表页面
     */
    public static function saveListPage ($tableName) {
        // 20240320
        $controller = DbOperate::getController($tableName);
        $tableKey   = DbOperate::getTableKey($tableName);
        
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
                $btnArr[] = ['name'=>'批量删除','cate'=>'listOperate','size'=>'mini','icon'=>'','type'=>'danger'
                    ,'data_url' =>'/admin/'.$controller.'/delRam?admKey='.$tableKey
                    ,'show_condition' =>'{"status":0}'
                    ,'confirm' =>'确认删除这些数据？'
                    ,'trigger'  =>'list'];
                UniversalItemBtnService::saveBtn($pageItem['id'],$btnArr);
            }
            if($pageItem['item_key'] == 'table'){
                UniversalItemTableService::saveField($pageItem['id'],$fields, $tableName);
                // 20240320
                $btnArrT = [];
                $btnArrT[] = ['name'=>'编辑','cate'=>'layerUniversal'      ,'size'=>'mini','icon'=>'','type'=>'','data_url'=>$baseName.'Add','param'=>'{"id":"id"}','trigger'=>'list'];
                $btnArrT[] = ['name'=>'删除','cate'=>'listOperate','size'=>'mini','icon'=>'','type'=>'danger'
                    ,'data_url' =>'/admin/'.$controller.'/delRam?admKey='.$tableKey
                    ,'show_condition' =>'{"status":0}'
                    ,'confirm' =>'确认删除这条数据？'
                    ,'trigger'  =>'list'];
                UniversalItemBtnService::saveBtn($pageItem['id'],$btnArrT);
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
        $controller = DbOperate::getController($tableName);
        $tableKey   = DbOperate::getTableKey($tableName);
        $sData['api_url'] = '/admin/'.$controller.'/get?admKey='.$tableKey;

        $pageId     = self::saveWithItemGetPageId($pageName, $items, $tableName, $sData);
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
                $btnArr[]   = ['name'=>'保存','cate'=>'listOperate'      ,'size'=>'mini','data_url'=>'/admin/'.$tableArr[1].'/saveGetInfoRam?admKey='.$urlKey ,'trigger'=>'close'];
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
        // $items      = ['detail','tab','detail'];
        //带项目保存，获取页面id
        $items = [];
        $items[] = ['item_key'=>'detail','data_url'=>'','show_condition'=>null,'value'=>null];
        $items[] = ['item_key'=>'tab','data_url'=>'','show_condition'=>'{"status":1}','value'=>'valTab'];
        $items[] = ['item_key'=>'detail','data_url'=>'','show_condition'=>'{"status":1,"valTab":"base"}','value'=>null];
        // 20230820改造
        $controller = DbOperate::getController($tableName);
        $tableKey   = DbOperate::getTableKey($tableName);
        $sData              = [];
        $sData['api_url']   = '/admin/'.$controller.'/get?admKey='.$tableKey;
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
     * 20220427保存列表页面
     */
    public static function saveWebListPage ($tableName) {
        $baseName   = self::tableNameGetWebBaseName($tableName);
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
    public static function saveWebAddPage($tableName){
        $baseName   = self::tableNameGetWebBaseName($tableName);
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
    public static function saveWebDetailPage($tableName){
        $baseName   = self::tableNameGetWebBaseName($tableName);
        $fieldsArr  = DbOperate::columns($tableName);
        // $fields     = array_column($fieldsArr,'Field');
        $expFields  = self::getExpFields();
        $fields     = array_diff(array_column($fieldsArr,'Field'),$expFields);
        // 添加页面
        $pageName   = $baseName."Info";
        $items      = ['detail'];
        //带项目保存，获取页面id
        $controller = DbOperate::getController($tableName);
        $tableKey   = DbOperate::getTableKey($tableName);        
        $sData['api_url']    = '/admin/'.$controller.'/get?admKey='.$tableKey;
        $pageId     = self::saveWithItemGetPageId($pageName, $items, $tableName, $sData);
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
}
