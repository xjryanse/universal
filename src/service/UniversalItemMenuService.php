<?php

namespace xjryanse\universal\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\user\service\UserAuthRoleUniversalService;

/**
 * 宫格
 */
class UniversalItemMenuService extends Base implements MainModelInterface {

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
    // 20240508:给AuthSetTrait用
    protected static $titleKey = 'title';
    use \xjryanse\universal\traits\MenuAuthSetTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\universal\\model\\UniversalItemMenu';
    //直接执行后续触发动作
    protected static $directAfter = true;

    /**
     * 必有方法
     */
    public static function optionArr($pageItemId) {
        $pageId = UniversalPageItemService::getInstance($pageItemId)->fPageId();
        $groupId = UniversalPageService::getInstance($pageId)->fGroupId();

        $info = UniversalPageItemService::getInstance($pageItemId)->get();

        $con[] = ['group_id', '=', $groupId];
        $con[] = ['status', '=', 1];
        if ($info['auth_check']) {
            $res = self::universalListWithAuth($con, false);
        } else {
            //0831:会影响前台
            $res = self::universalListWithAuth($con);
        }
        //$res = self::lists( $con ,'sort','id,page_key,title,icon_pic,grid_icon,class,icon_color,url');
        return $res;
    }

    /**
     * 钩子-保存前
     */
    public static function extraPreSave(&$data, $uuid) {
        //20220423保存角色
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
        //20220423保存角色
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
     * 20220819
     * @param type $ids
     * @return type
     */
    public static function extraDetails($ids) {
        return self::commExtraDetails($ids, function($lists) use ($ids) {
                    $universalTable = self::getTable();
                    foreach ($lists as &$v) {
                        $v['roleIds'] = UserAuthRoleUniversalService::universalRoleIds($universalTable, $v['id']);
                        $v['roleCount'] = count($v['roleIds']);
                    }
                    return $lists;
                });
    }
    /**
     * 20231009
     * @param type $pageItemId
     * @param type $newPageItemId
     * @return bool
     */
    public static function downLoadRemoteConf($pageItemId, $newPageItemId) {
        // 菜单各自设置，默认不同步
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
