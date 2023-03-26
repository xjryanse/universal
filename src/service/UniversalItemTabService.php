<?php

namespace xjryanse\universal\service;

use xjryanse\user\service\UserAuthRoleUniversalService;
use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\Strings;
use xjryanse\logic\Arrays2d;
use xjryanse\logic\DbOperate;
/**
 * tab标签页
 */
class UniversalItemTabService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

// 静态模型：配置式数据表
    use \xjryanse\traits\StaticModelTrait;
    use \xjryanse\universal\traits\UniversalTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\universal\\model\\UniversalItemTab';
    //直接执行后续触发动作
    protected static $directAfter = true;  

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
     * 必有方法
     */
    public static function optionArr($pageItemId) {
        $con[] = ['page_item_id', '=', $pageItemId];
        $con[] = ['status', '=', 1];
        // $info = UniversalPageItemService::mainModel()->where('id',$pageItemId)->find();
        $info = UniversalPageItemService::getInstance($pageItemId)->staticGet();
        if($info['auth_check']){
            // 20220825:带权限数据校验
            $resRaw = self::universalListWithAuth($con, false);
        } else {
            $resRaw = self::staticConList($con, '', 'sort');            
        }
        
        // 2022-12-17
        $hideKeys   = DbOperate::keysForHide(['page_id','page_item_id','sort','status']);
        $res        = Arrays2d::hideKeys($resRaw, $hideKeys);

        foreach ($res as $k => &$v) {
            $v['param'] = json_decode($v['param']);
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
