<?php

/**
 * 
 * 
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */

namespace cza\base\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * CZA ActiveRecord is the base class for classes representing relational data in terms of objects.
 *
 *
 * @author Ben Bi <ben@cciza.com>
 * @since 2.0
 */
class ActiveRecord extends \yii\db\ActiveRecord {

    public function behaviors() {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function getBaseFormName($isId = false) {
        $name = $this->formName() . '-base-form';
        return $isId ? "#" . $name : $name;
    }
    
    public function getDetailPjaxName($isId = false) {
        $name = $this->formName() . '-detail-pjax';
        return $isId ? "#" . $name : $name;
    }

    public function getTranslationFormName($isId = false) {
        $name = $this->formName() . "-trans-form-{$this->language}";
        return $isId ? "#" . $name : $name;
    }

    public function getTranslationPjaxName($isId = false) {
        $name = $this->formName() . "-trans-pjax-{$this->language}";
        return $isId ? "#" . $name : $name;
    }

}
