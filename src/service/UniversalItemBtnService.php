<?php

namespace xjryanse\universal\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\user\service\UserAuthRoleUniversalService;
use xjryanse\system\service\SystemAbilityPageKeyService;
use xjryanse\logic\Debug;
use xjryanse\logic\BaseSystem;
use xjryanse\logic\Arrays;
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
    use \xjryanse\traits\MainModelQueryTrait;

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
    public function getExportFieldArr($subKey = '') {
        //$pageId     = $this->getPageId() ? : $this->sysPageId() ;
        //$pageConf   = $pageId ? UniversalPageService::getPageWithSys($pageId) : [];

        $pageKey = $this->getPageKey() ?: $this->sysPageKey();
        if ($pageKey && $subKey) {
            $pageKey = $pageKey . '_' . $subKey;
        }
        $pageConf = $pageKey ? UniversalPageService::getPageWithSys($pageKey) : [];
        Debug::debug('$pageConf', $pageConf);
        if (!$pageConf) {
            throw new Exception('页面:' . $pageKey . '配置异常，请联系您的软件服务商' . __METHOD__);
        }
        $optArr = [];
        foreach ($pageConf['pageItems'] as $v) {
            if ($v['item_key'] == 'table') {
                $con = [];
                $con[] = ['is_export', '=', 1];
                $optArr = Arrays2d::listFilter($v['optionArr'], $con);
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
    public function getPageId() {
        $pageItemTable = UniversalPageItemService::getTable();
        $itemBtnTable = UniversalItemBtnService::getTable();
        $sql = "SELECT a.page_id FROM " . $pageItemTable . " AS a
            INNER JOIN ( SELECT * FROM " . $itemBtnTable . " WHERE id = '" . $this->uuid . "' ) AS b ON a.id = b.page_item_id";
        Debug::debug('getPageId的sql', $sql);
        $res = Db::query($sql);
        return $res ? $res[0]['page_id'] : '';
    }

    /**
     * 20230402:替代上述方法
     * @return type
     */
    public function getPageKey() {
        $pageItemTable = UniversalPageItemService::getTable();
        $itemBtnTable = UniversalItemBtnService::getTable();
        $sql = "SELECT a.page_id FROM " . $pageItemTable . " AS a
            INNER JOIN ( SELECT * FROM " . $itemBtnTable . " WHERE id = '" . $this->uuid . "' ) AS b ON a.id = b.page_item_id";
        Debug::debug('getPageId的sql', $sql);
        $res = Db::query($sql);
        $pageId = $res ? $res[0]['page_id'] : '';
        $pageInfo = UniversalPageService::getInstance($pageId)->get();

        return $pageInfo ? $pageInfo['page_key'] : '';
    }
    
    /**
     * 20230726:公司是否有某个按钮的权限
     * @param type $companyId
     * @param type $pageKey
     */
    public function compHasAuth(){
        // 1-提取该按钮的页面key
        $info = $this->get();
        $abiKey = Arrays::value($info,  'ability_key');
        // 2-如果没有页面key，默认有权限
        if(!$abiKey){
            return true;
        }
        // 3-提取系统全部有权能力key
        $abiArr = SystemAbilityPageKeyService::allAbilityArr();
        // 4-判断能力key是否在列表中
        return in_array($abiKey, $abiArr);
    }

    /**
     * 20230326
     * @return type
     */
    protected function sysPageId() {
        $param['id'] = $this->uuid;
        return BaseSystem::baseSysGet('/webapi/Universal/btnPageId', $param);
    }

    protected function sysPageKey() {
        $param['id'] = $this->uuid;
        return BaseSystem::baseSysGet('/webapi/Universal/btnPageKey', $param);
    }

    /**
     * 传一堆字段数组保存
     * @param type $pageItemId
     * @param type $btns
     */
    public static function saveBtn($pageItemId, array $btns) {
        self::checkTransaction();
        $dataArr = [];
        foreach ($btns as &$btn) {
            $tmp = $btn;
            $tmp['page_item_id'] = $pageItemId;
            $dataArr[] = $tmp;
        }
        $res = self::saveAll($dataArr);
        return $res;
    }

    /**
     * 必有方法
     */
    public static function optionArr($pageItemId, $subKey = '') {
        $con[] = ['page_item_id', '=', $pageItemId];
        $con[] = ['status', '=', 1];
        // $info = UniversalPageItemService::mainModel()->where('id',$pageItemId)->find();
        $info = UniversalPageItemService::getInstance($pageItemId)->staticGet();
        if ($info['auth_check']) {
            // 20220825:带权限数据校验
            $res = self::universalListWithAuth($con, false);
        } else {
            $res = self::staticConList($con, '', 'sort');
        }
        // 20230809:增加根据能力key,去除未开通功能的按钮
        foreach($res as $k=>$v){
            if(!self::getInstance($v['id'])->compHasAuth()){
                unset($res[$k]);
            }
        }
        // 2022-12-17
        $hideKeys = DbOperate::keysForHide(['page_id', 'page_item_id', 'sort', 'status']);
        // 20230908:增加array_values;
        $res = array_values(Arrays2d::hideKeys($res, $hideKeys));

        return self::jsonCov($res, $subKey);
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
    public function getWithOption() {
        $con[] = ['id', '=', $this->uuid];
        $con[] = ['status', '=', 1];
        $res = self::staticConList($con, '', 'sort');
        $resArr = self::jsonCov($res);
        return $resArr ? $resArr[0] : [];
    }

    /**
     * json 数据转换
     */
    protected static function jsonCov($res, $subKey = '') {
        // 20230329:兼容万能表单
        $data['subKey'] = $subKey;

        foreach ($res as &$v) {
            //20230329:兼容万能表单
            $tmp = json_encode($v, JSON_UNESCAPED_UNICODE);
            $v = json_decode(Strings::dataReplace($tmp, $data), JSON_UNESCAPED_UNICODE);

            $v['option'] = Strings::isJson($v['option']) ? json_decode($v['option']) : $v['option'];
            $v['show_condition'] = json_decode($v['show_condition']);
            $v['param'] = json_decode($v['param']);
            //20220627
            $v['clear_cache_key'] = explode(',', $v['clear_cache_key']);
            // 20230816，特殊处理:
            if($v['cate'] == 'wxOpenFile' && Strings::isSnowId($v['tpl_id'])){
                $v = Arrays::picFieldCov($v, ['tpl_id']);
            }
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
        return self::commExtraDetails($ids, function($lists) use ($ids) {
                    $universalTable = self::getTable();
                    foreach ($lists as &$v) {
                        $v['pageId']    = UniversalPageItemService::getInstance($v['page_item_id'])->fPageId();
                        // 20230730
                        $v['pageCate']  = UniversalPageService::getInstance($v['pageId'])->fCate();
                        // 20230603:页面id
                        $v['roleIds']   = UserAuthRoleUniversalService::universalRoleIds($universalTable, $v['id']);
                        $v['roleCount'] = count($v['roleIds']);
                    }
                    return $lists;
                });
    }

    /**
     * 20230331
     * @param type $options
     * @param type $newPageItemId
     * @return boolean
     */
    public static function downLoadRemoteConf($options, $newPageItemId) {
        self::checkTransaction();
        foreach ($options as $item) {
            $sData = $item;
            $newItemId = self::mainModel()->newId();
            $sData['id'] = $newItemId;
            $sData['page_item_id'] = $newPageItemId;
            self::save($sData);
        }
        return true;
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
