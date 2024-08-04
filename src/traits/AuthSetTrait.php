<?php
namespace xjryanse\universal\traits;

use xjryanse\user\service\UserAuthRoleUniversalService;
use xjryanse\user\service\UserAuthRoleService;
use xjryanse\logic\Arrays;
use xjryanse\logic\Arrays2d;
use think\facade\Request;
/**
 * 需要依赖
    use \xjryanse\traits\StaticModelTrait;
 */
trait AuthSetTrait {
    /**
     * 权限设置用的分页：
     * 
     */
    public static function paginateForAuthSet(){
        $param                  = Request::param('table_data');
        $pageItemId             = Arrays::value($param, 'page_item_id');
        $res['data']            = self::listForAuthSet($pageItemId);
        $res['fdynFields']      = self::fDynFieldsForAuthSet($pageItemId);
        $res['per_page']        = 9999;
        $res['total']           = count($res['data']);
        $res['current_page']    = 1;

        return $res;
    }
    
    /**
     * 20240413：用于动态设置权限
     */
    protected static function listForAuthSet($pageItemId){
        $titleKey = self::$titleKey;
        // 提取角色列表
        $roleList = UserAuthRoleService::staticConList();
        // 提取菜单项列表
        $con = [['page_item_id','=',$pageItemId]];
        $tabList = self::staticConList($con);
        // 权限表
        $itemIds = Arrays2d::uniqueColumn($tabList, 'id');
        $universalRoleIds = UserAuthRoleUniversalService::groupBatchColumn('universal_id', $itemIds, 'role_id');
        
        
        $arr = [];
        foreach($roleList as $role){
            $tmp                = [];
            $tmp['id']          = $role['id'];
            // 更新用
            $tmp['role_id']     = $role['id'];
            $tmp['roleName']    = $role['name'];
            foreach($tabList as $tab){
                $uRoleIds           = Arrays::value($universalRoleIds, $tab['id']) ? : [];
                // 菜单项名称
                // $titleKey = 'tab_title';
                $tmp['n'.$tab['id']] = $tab[$titleKey]; 
                // 菜单项有权限？
                $tmp['a'.$tab['id']] = in_array($role['id'], $uRoleIds) ? 1 : 0; 
            }
            // $tmp['$universalRoleIds'] = $universalRoleIds;
            $arr[] = $tmp;
        }
        
        return $arr;
    }
    
    /**
     * 权限设置用的动态字段
     * 开关形式，方便配置
     * @param type $pageItemId
     * @return string
     */
    protected static function fDynFieldsForAuthSet($pageItemId) {
        $titleKey = self::$titleKey;
        $con[]  = ['page_item_id', '=', $pageItemId];
        $con[]  = ['status', '=', 1];
        $lists  = self::staticConList($con);
        $arr    = [];
        foreach ($lists as $v) {
            $arr[] = ['id'=>$v['id'],'name'=>'a'.$v['id'],'label'=>$v[$titleKey],'type'=>'switch'
                ,'update_url'=>'/admin/user/ajaxOperateFullP?admKey=authRoleUniversal&doMethod=doUpdateByRoleAndId'
                ,'update_param'=>[
                    'role_id'           =>  'role_id'
                    ,'hasAuth'          =>  'a'.$v['id']
                    ,'universal_table'  =>  self::getTable()
                    ,'universal_id'     =>  $v['id']
                ]
            ];
        }

        return $arr;
    }
}
