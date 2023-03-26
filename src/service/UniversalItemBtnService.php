<?php

namespace xjryanse\universal\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\user\service\UserAuthRoleUniversalService;
use xjryanse\logic\Debug;
use xjryanse\logic\Arrays2d;
use xjryanse\logic\DbOperate;
use xjryanse\logic\Strings;
use think\Db;
use Exception;

/**
 * 按钮
 */
class UniversalItemBtnService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

// 静态模型：配置式数据表
    use \xjryanse\traits\StaticModelTrait;
    use \xjryanse\universal\traits\UniversalTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\universal\\model\\UniversalItemBtn';
    //直接执行后续触发动作
    protected static $directAfter = true;

    /**
     * 导出数据表时，获取字段列表
     * TODO更科学？？只提取第一个表格
     */
    public function getExportFieldArr(){
        $pageId     = $this->getPageId() ? : $this->sysPageId() ;
        $pageConf   = $pageId ? UniversalPageService::getPageWithSys($pageId) : [];
        Debug::debug('$pageConf', $pageConf);
        if(!$pageConf){
            throw new Exception('页面:'.$pageId.'配置异常，请联系您的软件服务商'.__METHOD__);
        }
        $optArr = [];
        foreach($pageConf['pageItems'] as $v){
            if($v['item_key'] == 'table'){
                $con    = [];
                $con[]  = ['is_export','=',1];
                $optArr = Arrays2d::listFilter($v['optionArr'],$con);
                break;
            }
        }
        return $optArr;
    }
    /**
     * 根据按钮id，获取所属页面id
     * 一般用于反取页面
     * @return type
     */
    public function getPageId(){
        $pageItemTable  = UniversalPageItemService::getTable();
        $itemBtnTable   = UniversalItemBtnService::getTable();
        $sql = "SELECT a.page_id FROM ".$pageItemTable." AS a
            INNER JOIN ( SELECT * FROM ".$itemBtnTable." WHERE id = '".$this->uuid."' ) AS b ON a.id = b.page_item_id";
        Debug::debug('getPageId的sql', $sql);
        $res = Db::query($sql);
        return $res ? $res[0]['page_id'] : '';
    }
    /**
     * 20230326
     * @return type
     */
    protected function sysPageId(){
        $param['id']    = $this->uuid;
        return $this->baseSysGet('/webapi/Universal/btnPageId',$param);
    }
    /**
     * 传一堆字段数组保存
     * @param type $pageItemId
     * @param type $btns
     */
    public static function saveBtn($pageItemId, array $btns){
        self::checkTransaction();
        $dataArr = [];
        foreach($btns as &$btn){
            $tmp = $btn;
            $tmp['page_item_id']    = $pageItemId;
            $dataArr[] = $tmp;
        }
        $res = self::saveAll($dataArr);
        return $res;
    }
    /**
     * 必有方法
     */
    public static function optionArr($pageItemId) {
        $con[] = ['page_item_id', '=', $pageItemId];
        $con[] = ['status', '=', 1];
        // $info = UniversalPageItemService::mainModel()->where('id',$pageItemId)->find();
        $info = UniversalPageItemService::getInstance($pageItemId)->staticGet();
        if($info['auth_check']){
            // 20220825:带权限数据校验
            $res = self::universalListWithAuth($con, false);
        } else {
            $res = self::staticConList($con, '', 'sort');            
        }
        // 2022-12-17
        $hideKeys   = DbOperate::keysForHide(['page_id','page_item_id','sort','status']);
        $res        = Arrays2d::hideKeys($res, $hideKeys);

        return self::jsonCov($res);
    }

    public static function subOptionArr($subItemId) {
        $con[] = ['subitem_id', '=', $subItemId];
        $con[] = ['status', '=', 1];

        $res = self::staticConList($con, '', 'sort');
        return self::jsonCov($res);
    }
    /**
     * 带选项获取值
     */
    public function getWithOption(){
        $con[] = ['id', '=', $this->uuid];
        $con[] = ['status', '=', 1];
        $res = self::staticConList($con, '', 'sort');
        $resArr = self::jsonCov($res);
        return $resArr ? $resArr[0] : [];
    }
    /**
     * json 数据转换
     */
    protected static function jsonCov($res){
        foreach ($res as &$v) {
            $v['option']            = Strings::isJson($v['option']) ? json_decode($v['option']) : $v['option'];
            $v['show_condition']    = json_decode($v['show_condition']);
            $v['param']             = json_decode($v['param']);
            //20220627
            $v['clear_cache_key']   = explode(',',$v['clear_cache_key']);
        }
        return $res;
    }

    /**
     * 钩子-保存前
     */
    public static function extraPreSave(&$data, $uuid) {
        if (isset($data['roleIds'])) {
            self::getInstance($uuid)->roleUniversalSave($data['roleIds']);
        }
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
        if (isset($data['roleIds'])) {
            self::getInstance($uuid)->roleUniversalSave($data['roleIds']);
        }
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
        $this->universalRoleClear();
    }
    
    public static function extraDetails($ids) {
        return self::commExtraDetails($ids, function($lists) use ($ids){            
            $universalTable = self::getTable();
            foreach ($lists as &$v) {
                $v['roleIds']    = UserAuthRoleUniversalService::universalRoleIds($universalTable, $v['id']);
                $v['roleCount']      = count($v['roleIds']);
            }            
            return $lists;
        });
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

}
