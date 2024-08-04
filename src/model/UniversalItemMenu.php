<?php
namespace xjryanse\universal\model;

/**
 * 菜单
 */
class UniversalItemMenu extends Base
{
    use \xjryanse\traits\ModelUniTrait;
    // 20230516:数据表关联字段
    public static $uniFields = [
        [
            'field'     =>'group_id',
            'uni_name'  =>'universal_group',
            'uni_field' =>'id',
            'del_check' => true
        ],
    ];
    //20230728 是否将数据缓存到文件
    public static $cacheToFile = true;
    
    public static $picFields = ['icon_pic','icon_pic_selected'];
    
    public function setIconPicAttr($value) {
        return self::setImgVal($value);
    }

    public function getIconPicAttr($value) {
        return self::getImgVal($value);
    }
    
    public function setIconPicSelectedAttr($value) {
        return self::setImgVal($value);
    }

    public function getIconPicSelectedAttr($value) {
        return self::getImgVal($value);
    }

}