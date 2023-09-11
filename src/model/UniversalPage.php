<?php
namespace xjryanse\universal\model;

/**
 * 页面表
 */
class UniversalPage extends Base
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
    
    /**
     * 20230908：反置属性
     * @var type
     */
    public static $uniRevFields = [
        [
            'table'         =>'wechat_we_app_qr_scene',
            'field'         =>'from_table_id',
            'uni_field'     =>'id',
            'exist_field'   =>'isWechatWeAppQrSceneExist',
            'condition'     =>[
                // 关联表，即本表
                'from_table'=>'{$uniTable}'
            ]
        ],
    ];
    
    
    //20230728 是否将数据缓存到文件
    public static $cacheToFile = true;
    
    public static $picFields = ['share_img'];
    
    // 分享图标
    public function setShareImgAttr($value) {
        return self::setImgVal($value);
    }
    // 分享图标
    public function getShareImgAttr($value) {
        return self::getImgVal($value);
    }

}