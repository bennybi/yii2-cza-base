<?php

/**
 * 
 * 
 * @author Ben Bi <bennybi@qq.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */

namespace cza\base\models\abstracts;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\validators\Validator;
use cza\base\models\statics\EntityModelStatus;
use \yii\behaviors\BlameableBehavior;

/**
 * CZA ActiveRecord is the base class for classes representing relational data in terms of objects.
 *
 * @property integer $id
 * @property integer $owner_id
 * @property integer $type
 * @property string $code
 * @property string $label
 * @property string $default_value
 * @property string $custom_value
 * @property string $memo
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $status
 * @property integer $position
 * @property string $created_at
 * @property string $updated_at
 * @author Ben Bi <bennybi@qq.com>
 * @since 2.0
 */
abstract class Config extends \cza\base\models\ActiveRecord {

    public function init() {
        parent::init();
    }

    public function behaviors() {
        return ArrayHelper::merge(parent::behaviors(), [
                    [
                        'class' => BlameableBehavior::className(),
                        'createdByAttribute' => 'created_by',
                        'updatedByAttribute' => 'updated_by'
                    ]
        ]);
    }

    public function saveDataItems($items) {
        $this->_data['custom_value'] = $items;
        $this->custom_value = \json_encode($this->_data['custom_value']);
        return $this->updateAttributes([
                    'custom_value' => $this->custom_value,
        ]);
    }

    public function saveDataItem($key, $val) {
        $this->_data['custom_value'] = \json_decode($this->custom_value, true);
        $this->_data['custom_value'][$key] = $val;
        $this->custom_value = \json_encode($this->_data['custom_value']);
        return $this->updateAttributes([
                    'custom_value' => $this->custom_value,
        ]);
    }

    public function setDataItem($k, $v) {
        $this->_data['custom_value'][$key] = $val;
    }

    public function getDataToArray() {
        if (!isset($this->_data['custom_value'])) {
            if (empty($this->default_value)) {
                $this->_data['custom_value'] = \json_decode($this->custom_value, true);
            } else {
                $this->_data['custom_value'] = ArrayHelper::merge(\json_decode($this->default_value, true), \json_decode($this->custom_value, true));
            }
        }
        return $this->_data['custom_value'];
    }

    /**
     * 获取custom_value 某个字段的值
     */
    public function getDataItem($key, $default = null) {
        return ArrayHelper::getValue($this->getDataToArray(), $key, $default);
    }

}
