<?php

namespace xjryanse\universal\service;

use xjryanse\user\service\UserAuthRoleUniversalService;
use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\user\service\UserAuthRoleService;
use xjryanse\logic\Arrays2d;
use xjryanse\logic\DbOperate;
/**
 * tab标签页
 */
class UniversalItemTabService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    // 静态模型：配置式数据表
    use \xjryanse\traits\StaticModelTrait;
    use \xjryanse\universal\traits\UniversalTrait;

    // 20240504:给AuthSetTrait用
    protected static $titleKey = 'tab_title';
    use \xjryanse\universal\traits\AuthSetTrait;

    use \xjryanse\universal\service\itemTab\DimTraits;
    use \xjryanse\universal\service\itemTab\FieldTraits;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\universal\\model\\UniversalItemTab';
    //直接执行后续触发动作
    protected static $directAfter = true;
    // 20231008
    protected static $itemKey = 'tab';
    
    public static function extraDetails($ids) {
        return self::commExtraDetails($ids, function($lists) use ($ids) {
                    $universalTable = self::getTable();
                    foreach ($lists as &$v) {
                        $v['roleIds']   = UserAuthRoleUniversalService::universalRoleIds($universalTable, $v['id']);
                        $v['roleNames'] = UserAuthRoleService::idNames($v['roleIds']);
                        $v['roleCount'] = count($v['roleIds']);
                        //2030607:页面存在
                        $v['isTabPageExist'] = UniversalPageService::getByPageKey($v['pc_page_key']) ? 1 : 0;
                    }
                    return $lists;
                });
    }

    /**
     * 必有方法
     */
    public static function optionArr($pageItemId) {
        $con[] = ['page_item_id', '=', $pageItemId];
        $con[] = ['status', '=', 1];
        // $info = UniversalPageItemService::mainModel()->where('id',$pageItemId)->find();
        $info = UniversalPageItemService::getInstance($pageItemId)->staticGet();
        if ($info['auth_check']) {
            // 20220825:带权限数据校验
            $resRaw = self::universalListWithAuth($con, false);
        } else {
            $resRaw = self::staticConList($con, '', 'sort');
        }

        // 2022-12-17
        $hideKeys = DbOperate::keysForHide(['page_id', 'page_item_id', 'sort', 'status']);
        $res = Arrays2d::hideKeys($resRaw, $hideKeys);

        foreach ($res as $k => &$v) {
            $v['param'] = json_decode($v['param']);
            //展示条件
            $v['show_condition'] = json_decode($v['show_condition']);
        }
        foreach ($res as $k => &$v) {
            //每月的天数
            if ($v['tab_key'] == 'MONTH_DAY') {
                $data[] = ['tab_key' => '', 'tab_title' => '全部'];
                for ($i = 1; $i <= 31; $i++) {
                    $data[] = ['tab_key' => str_pad($i, 2, "0", STR_PAD_LEFT), 'tab_title' => $i, 'show_statics' => $v['show_statics']];
                }

                array_splice($res, $k, 1, $data);
            }
            //20231028：12个月
            if ($v['tab_key'] == 'YEAR_MONTH') {
                $data[] = ['tab_key' => '', 'tab_title' => '全部'];
                for ($i = 1; $i <= 12; $i++) {
                    $data[] = ['tab_key' => str_pad($i, 2, "0", STR_PAD_LEFT), 'tab_title' => $i, 'show_statics' => $v['show_statics']];
                }

                array_splice($res, $k, 1, $data);
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
    /**
     * 20230727
     * @param type $pageItemId
     * @param type $newPageItemId
     * @return bool
     */
    public static function downLoadRemoteConf($pageItemId, $newPageItemId) {
        // 表格字段
        return self::universalSysItemsDownload($pageItemId, $newPageItemId);
    }

    
}
