<?php
namespace xjryanse\universal\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 万能表单页面项目
 */
class UniversalPageItemService extends Base implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\universal\\model\\UniversalPageItem';
    /**
     * 根据页面id筛选
     * @param type $pageId
     * @return type
     */
    public static function selectByPageId($pageId){
        $con[] = ['page_id','=',$pageId];
        $con[] = ['status','=',1];
        $lists = self::lists($con,'sort','id,page_id,item_key,data_url,class,value');
        foreach($lists as &$v){
            $classStr = UniversalItemService::getClassStr($v['item_key']);
            //配置选项
            $v['optionArr'] = class_exists($classStr) ? $classStr::optionArr($v['id']) : [];
        }
        return $lists;
    }
    
    /**
     * 钩子-保存前
     */
    public static function extraPreSave(&$data, $uuid) {
        
    }
    /**
     * 钩子-保存后
     */
    public static function extraAfterSave(&$data, $uuid) {

    }
    /**
     * 钩子-更新前
     */
    public static function extraPreUpdate(&$data, $uuid) {

    }
    /**
     * 钩子-更新后
     */
    public static function extraAfterUpdate(&$data, $uuid) {

    }    
    /**
     * 钩子-删除前
     */
    public function extraPreDelete()
    {

    }
    /**
     * 钩子-删除后
     */
    public function extraAfterDelete()
    {

    }
    
    /**
	 *
	 */
	public function fId()
	{
		return $this->getFFieldValue(__FUNCTION__);	
	}
	/**
	 *页面id
	 */
	public function fPageId()
	{
		return $this->getFFieldValue(__FUNCTION__);	
	}
	/**
	 *项目key
	 */
	public function fItemKey()
	{
		return $this->getFFieldValue(__FUNCTION__);	
	}
	/**
	 *排序
	 */
	public function fSort()
	{
		return $this->getFFieldValue(__FUNCTION__);	
	}
	/**
	 *状态(0禁用,1启用)
	 */
	public function fStatus()
	{
		return $this->getFFieldValue(__FUNCTION__);	
	}
	/**
	 *有使用(0否,1是)
	 */
	public function fHasUsed()
	{
		return $this->getFFieldValue(__FUNCTION__);	
	}
	/**
	 *锁定（0：未锁，1：已锁）
	 */
	public function fIsLock()
	{
		return $this->getFFieldValue(__FUNCTION__);	
	}
	/**
	 *锁定（0：未删，1：已删）
	 */
	public function fIsDelete()
	{
		return $this->getFFieldValue(__FUNCTION__);	
	}
	/**
	 *备注
	 */
	public function fRemark()
	{
		return $this->getFFieldValue(__FUNCTION__);	
	}
	/**
	 *创建者，user表
	 */
	public function fCreater()
	{
		return $this->getFFieldValue(__FUNCTION__);	
	}
	/**
	 *更新者，user表
	 */
	public function fUpdater()
	{
		return $this->getFFieldValue(__FUNCTION__);	
	}
	/**
	 *创建时间
	 */
	public function fCreateTime()
	{
		return $this->getFFieldValue(__FUNCTION__);	
	}
	/**
	 *更新时间
	 */
	public function fUpdateTime()
	{
		return $this->getFFieldValue(__FUNCTION__);	
	}
	
}
