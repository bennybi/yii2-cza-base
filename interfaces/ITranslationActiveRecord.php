<?php
/**
 * 
 * 
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
namespace cza\base\interfaces;

/**
 * 
 */
interface ITranslationActiveRecord
{
    /**
     * for example:
     * public function getSrcModel(){
     *   return $this->hasOne(CmsPage::className(), ['id' => 'entity_id']);
     * }
     * @return source model
     */
    public function getSrcModel();
}
