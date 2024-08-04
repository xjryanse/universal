<?php
namespace xjryanse\universal\model;

/**
 * 个人中心菜单
 */
class UniversalItemCell extends Base
{
    //20230728 是否将数据缓存到文件
    public static $cacheToFile = true;
    
    use \xjryanse\traits\ModelUniTrait;
    // 20230516:数据表关联字段
    public static $uniFields = [
        [
            'field'     =>'page_item_id',
            'uni_name'  =>'universal_page_item',
            'uni_field' =>'id',
            'del_check' => true
        ],
    ];
    
    public static $picFields = ['icon_img'];
    
    /**
     * 用户头像图标
     * @param type $value
     * @return type
     */
    public function getIconImgAttr($value) {
        return self::getImgVal($value);
    }

    /**
     * 图片修改器，图片带id只取id
     * @param type $value
     * @throws \Exception
     */
    public function setIconImgAttr($value) {
        return self::setImgVal($value);
    }
}