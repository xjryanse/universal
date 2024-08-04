<?php
namespace xjryanse\universal\traits;

use xjryanse\user\service\UserAuthRoleUniversalService;
use xjryanse\user\service\UserAuthRoleService;
use xjryanse\logic\Arrays;
use xjryanse\logic\Arrays2d;
use think\facade\Request;
/**
 * 手机菜单的权限配置
    use \xjryanse\traits\StaticModelTrait;
 */
trait MenuAuthSetTrait {
    /**
     * 权限设置用的分页：
     * 
     */
    public static function paginateForAuthSet(){
        $param                  = Request::param('table_data');
        $groupId                = Arrays::value($param, 'group_id');
        $res['data']            = self::listForAuthSet($groupId);
        $res['fdynFields']      = self::fDynFieldsForAuthSet($groupId);
        $res['per_page']        = 9999;
        $res['total']           = count($res['data']);
        $res['current_page']    = 1;

        return $res;
    }
    
    /**
     * 20240413：用于动态设置权限
     */
    protected static function listForAuthSet($groupId){
        $titleKey = self::$titleKey;
        // 提取角色列表
        $roleList = UserAuthRoleService::staticConList();
        // 提取菜单项列表
        $con    = [];
        $con[]  = ['group_id','=',$groupId];
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
     * @param type $groupId
     * @return string
     */
    protected static function fDynFieldsForAuthSet($groupId) {
        $titleKey = self::$titleKey;
        $con[]  = ['group_id', '=', $groupId];
        $con[]  = ['status', '=', 1];
        $con[]  = [$titleKey,'<>',''];        
        $lists  = self::staticConList($con,'','sort');
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
