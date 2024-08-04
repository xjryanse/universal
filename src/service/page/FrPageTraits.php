<?php

namespace xjryanse\universal\service\page;

use xjryanse\logic\Arrays;
// use xjryanse\logic\Strings;
use xjryanse\logic\TplEngine;
use xjryanse\universal\service\UniversalPageItemService;
use Exception;
use think\facade\Request;
use think\facade\Env;
/**
 * 前端后台页面
 */
trait frPageTraits{

    /**
     * 页面生成
     */
    public function frPageGenerate(){
        if(Request::ip() != '::1'){
            throw new Exception('非本地环境不允许执行');
        }
        $page = $this->get();
        $contentRaw = self::frTplContent();
        

        //【2】页面项目
        $con[] = ['page_id','=',$this->uuid];
        $con[] = ['status','=',1];
        $items = UniversalPageItemService::mainModel()->where( $con )->order('sort,id')->select();

        $pItemArr       = [];
        $jsMethodsArr   = [];
        foreach($items as &$v){
            $pItemArr[]     = UniversalPageItemService::getInstance($v['id'])->frItemContent();
            $jsMethodsArr[] = UniversalPageItemService::getInstance($v['id'])->frJsMethod();
        }
        // 前端缩进空格
        $preBlanks = '    ';
        $page['pageItemsContent'] = implode($preBlanks,$pItemArr);
        $page['jsMethods'] = implode($preBlanks,$jsMethodsArr);
        // 20240427:模板引擎
        $inst       = new TplEngine($contentRaw);
        $inst->assign($page);
        $content    = $inst->displayStr();
        
        $pageKey = Arrays::value($page, 'page_key');
        return self::frGnt($content, $pageKey);
        
    }
    /**
     * 模板
     * @return type
     */
    private static function frTplContent(){
        $ds = DIRECTORY_SEPARATOR;
        $stub       = __DIR__ . $ds . '..'.$ds.'..'.$ds.'frPageTpl' . $ds . 'pcAdmin'. $ds .'page.stub';
        $contentRaw = file_get_contents($stub);
        
        
        
        
        
        return $contentRaw;
    }
    
    private static function frGnt($content, $pageKey, $overWrite = true){
        $ds = DIRECTORY_SEPARATOR;

        $filePath = Env::get('root_path') .'vue-admin'.$ds.'src'.$ds.'views'.$ds.'pages'.$ds. $pageKey .'.vue';
        if(file_exists($filePath) && !$overWrite){
            throw new Exception( $filePath. '文件已存在，不能创建!');
        }
        
        //不存在时，创建文件目录
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname( $filePath ), 0755, true);
        }
        //生成后的文件内容写入
        $res = file_put_contents($filePath, $content);
        return $res;
    }
    

}
