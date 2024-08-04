<?php

namespace xjryanse\universal\service\page;

use xjryanse\system\service\SystemInstructionsService;
use xjryanse\logic\Arrays;
/**
 * 字段复用列表
 */
trait DoTraits{

    /**
     * 复制页面字段
     */
    public function doSyncFieldName($param){
        return $this->syncFieldName();
    }
    /**
     * 删整页，含各页面项
     */
    public function doDelRecur(){
        return $this->delRecur();
        
        
    }
    
    /**
     * 20230914：添加说明
     */
    public function doAddInstruction(){
        $fromTable      = self::getTable();
        $fromTableId    = $this->uuid;

        $instructionId = SystemInstructionsService::getIdByFromTableIdWithGenerate($fromTable, $fromTableId);
        $data['instruction_id'] = $instructionId;
        return $this->updateRam($data);
    }
    /**
     * 复制页面
     * @param type $param
     */
    public static function doCopyPage($param){
        // 当前页面key
        $pageKey        = Arrays::value($param, 'pageKey');
        // 目标页面key
        $targetPageKey  = Arrays::value($param, 'targetPageKey');
        
        $pageId         = self::keyToId($pageKey);
        
        $res = self::getInstance($pageId)->copyPage($targetPageKey);
        return $res;
    }

}
