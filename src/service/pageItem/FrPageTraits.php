<?php

namespace xjryanse\universal\service\pageItem;

use xjryanse\logic\Strings;
use xjryanse\logic\DbOperate;
use xjryanse\logic\TplEngine;
/**
 * 前端后台页面
 */
trait FrPageTraits{

    private function getItemKeyService(){
        $info       = $this->get();
        $prefix     = config('database.prefix');
        $tableName  = $prefix . 'universal_item_' . $info['item_key'];
        $service    = DbOperate::getService($tableName);
        if (!class_exists($service) || !DbOperate::hasField($tableName, 'page_item_id')) {
            return false;
        }
        return $service;
    }
    
    private function itemKeyItems(){
        $service = $this->getItemKeyService();
        
        $con[] = ['page_item_id', '=', $this->uuid];
        $con[] = ['status', '=', 1];
        $items = $service::where($con)->order('sort,id')->select();
        return $items;
    }
    
    /**
     * 获取生成内容， 一般由页面的生成方法进行生成
     */
    public function frItemContent(){
        $info       = $this->get();

        $service = $this->getItemKeyService();
        $items = $this->itemKeyItems();
        
        $pItemArr = [];
        foreach ($items as &$v) {
            // TODO：无该方法应有一个报错机制
            if(method_exists($service, 'frItemContent')){
                $pItemArr[]  = $service::getInstance($v['id'])->frItemContent();
            }
        }
        // 前端缩进空格
        // $preBlanks = '    ';
        $spaceNum = 6;
        $info['itemsContent'] = Strings::addPreLineSpaces(implode('',$pItemArr),$spaceNum);
        
        // 20240427:模板引擎
        $contentRaw = self::frTplContent($info['item_key']);
        $inst       = new TplEngine($contentRaw);
        $inst->assign($info);
        $content    = $inst->displayStr();
        // 内容
        // $content    = Strings::dataReplace($contentRaw, $info);

        return $content;
    }
    /**
     * 模板
     * @return type
     */
    private static function frTplContent($itemKey){
        $stub       = self::frTplBaseUrl().$itemKey.'.stub';
        $contentRaw = file_get_contents($stub);
        return $contentRaw;
    }
    
    private static function frTplBaseUrl(){
        $ds         = DIRECTORY_SEPARATOR;
        $basePath   = __DIR__ . $ds . '..'.$ds.'..'.$ds.'frPageTpl' . $ds . 'pcAdmin'. $ds. 'pageItems'. $ds;
        return $basePath;
    }
    
    public function frJsMethod(){
        return '';
        /*
        $info       = $this->get();

        $service = $this->getItemKeyService();
        $items = $this->itemKeyItems();
        
        $pItemArr = [];
        foreach ($items as &$v) {
            // TODO：无该方法应有一个报错机制
            if(method_exists($service, 'frJsMethodContent')){
                $pItemArr[]  = $service::getInstance($v['id'])->frJsMethodContent();
            }
        }
        // 前端缩进空格
        // $preBlanks = '    ';
        $spaceNum = 6;
        $info['jsContent'] = Strings::addPreLineSpaces(implode('',$pItemArr),$spaceNum);
        
        // 20240427:模板引擎
        $contentRaw = self::frTplContent($info['item_key']);
        $inst       = new TplEngine($contentRaw);
        $inst->assign($info);
        $content    = $inst->displayStr();
        // 内容
        // $content    = Strings::dataReplace($contentRaw, $info);

        return $content;
         * 
         */
    }
    
}
