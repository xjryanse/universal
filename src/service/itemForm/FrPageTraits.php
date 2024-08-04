<?php

namespace xjryanse\universal\service\itemForm;

use xjryanse\logic\Strings;
use xjryanse\logic\DbOperate;
use xjryanse\logic\TplEngine;
/**
 * 前端后台页面
 */
trait FrPageTraits{

    /**
     * 获取生成内容， 一般由页面的生成方法进行生成
     */
    public function frItemContent(){
        $infoRaw = $this->get();
        $info = self::addOpt($infoRaw);

        $contentRaw = self::frTplContent($info['type']);
        // 20240427:模板引擎
        $inst       = new TplEngine($contentRaw);
        $inst->assign($info);
        $content    = $inst->displayStr();
        
        return $content;
    }
    /**
     * 模板
     * @return type
     */
    private static function frTplContent($type){
        $stub       = self::frTplBaseUrl().$type.'.stub';
        $contentRaw = file_get_contents($stub);
        return $contentRaw;
    }
    
    private static function frTplBaseUrl(){
        $ds         = DIRECTORY_SEPARATOR;
        $basePath   = __DIR__ . $ds . '..'.$ds.'..'.$ds.'frPageTpl' . $ds . 'pcAdmin'. $ds. 'pageItems'. $ds.'itemForm'. $ds;
        return $basePath;
    }

}
