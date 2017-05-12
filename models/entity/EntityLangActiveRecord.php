<?php

/**
 * 
 * 
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */

namespace cza\base\models\entity;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\validators\Validator;
use cza\base\models\statics\EntityModelStatus;

/**
 * CZA ActiveRecord is the base class for classes representing relational data in terms of objects.
 *
 *
 * @author Ben Bi <ben@cciza.com>
 * @since 2.0
 */
class EntityLangActiveRecord extends \cza\base\models\ActiveRecord {

    public function init() {
        parent::init();
    }

    public function getEntityModel() {
        $class = $this->getEntityClass();
        return $this->hasOne($class::className(), ['id' => 'entity_id']);
    }

    public function getEntityClass() {
        $name = $this->getClass();
        return substr($name, 0, -4);
    }

    public function getCamel2IdFormName() {
        if (!isset($this->_data['camelFormName'])) {
            $this->_data['camelFormName'] = Inflector::camel2id($this->formName()) . "-{$this->language}";
        }
        return $this->_data['camelFormName'];
    }

    public function getLangAttributeName($name) {
        return Inflector::camel2id($name) . "-{$this->language}";
    }

//    public function __get($name) {
//        if (strncasecmp($name, 'lang_', 5) === 0) {
//            return $this->getLangAttribute($name);
//        } else
//            return parent::__get($name);
//    }
//
//    public function __set($name, $value) {
//        if (strncasecmp($name, 'lang_', 5) === 0) {
//            $this->setLangAttribute($name, $value);
//        }
//        return parent::__set($name, $value);
//    }
//
//    public function isLangAttribute($name) {
//        return (strncasecmp($name, 'lang_', 4) === 0);
//    }
//
//    public function getLangAttribute($name) {
//        $attribute = substr($name, strpos($name, '-') + 1);
//        return $this->getAttribute($attribute);
//    }
//
//    public function setLangAttribute($name, $value) {
//        $attribute = substr($name, strpos($name, '-') + 1);
//        $this->setAttribute($attribute, $value);
//    }
//
//    public function setAttributes($values, $safeOnly = true) {
//        if (!is_array($values)) {
//            return;
//        }
//        
//        $attributes = array_flip($safeOnly ? $this->getSafeAttributeNames() : $this->attributeNames());
//        foreach ($values as $name => $value) {
//            if ($this->isLangAttribute($name)) {
//                $this->setLangAttribute($name, $value);
//            } elseif (isset($attributes[$name])) {
//                $this->$name = $value;
//            } elseif ($safeOnly) {
//                $this->onUnsafeAttribute($name, $value);
//            }
//        }
//    }
//
//    public function attributeLabels() {
//        if ($this->item) {
//            return $this->item->attributeLabels();
//        }
//        return array();
//    }
}
