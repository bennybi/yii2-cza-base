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
use yii\validators\Validator;
use cza\base\models\statics\EntityModelStatus;

/**
 * CZA ActiveRecord is the base class for classes representing relational data in terms of objects.
 *
 *
 * @author Ben Bi <ben@cciza.com>
 * @since 2.0
 */
class ActiveRecord extends \yii\db\ActiveRecord {

    use ModelTrait;

    const SCENARIO_UPLOAD = 'upload';

    // for caching variables, e.g paths, logic names
//    protected $_data = [];

    public function getStatusLabel() {
        return EntityModelStatus::getLabel($this->status);
    }

    public function behaviors() {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => function () {
                    return date('Y-m-d H:i:s');
                },
            ],
        ];
    }

    /**
     * @param string $keyField Keyword
     * @param string $valFiled Value
     * @return key-value array
     */
    public static function getHashMap($keyField, $valField, $condition = '') {
        $class = static::className();
        return ArrayHelper::map($class::find()->select([$keyField, $valField])->andWhere($condition)->asArray()->all(), $keyField, $valField);
    }

    /**
     * return a format data array for select2 ajax response
     * @param type $keyField
     * @param type $valField
     * @param type $condition
     * @return array
     */
    public static function getOptionsList($keyField, $valField, $condition = '', $params = ['limit' => 50]) {
        $class = static::className();
        return $class::find()->select([$keyField, $valField])->andWhere($condition)->limit($params['limit'])->asArray()->all();
    }

    /**
     * return a format data array for select2 ajax response
     * @param type $keyField
     * @param type $valField
     * @param type $condition
     * @return array
     */
    public static function getOptionsListCallable($keyField, $valField, $condition = '', $params = []) {
        $params = ArrayHelper::merge(['limit' => 10], $params);
        $class = static::className();
        $items = [];
        $models = $class::find()->andWhere($condition)->limit($params['limit'])->all();
        foreach ($models as $model) {
            $items[] = [
                $keyField => $model->$keyField,
                $valField => $model->$valField,
            ];
        }
        return $items;
    }

    /**
     * Adds a validation rule to this model.
     * You can also directly manipulate [[validators]] to add or remove validation rules.
     * This method provides a shortcut.
     * @param string|array $attributes the attribute(s) to be validated by the rule
     * @param mixed $validator the validator for the rule.This can be a built-in validator name,
     * a method name of the model class, an anonymous function, or a validator class name.
     * @param array $options the options (name-value pairs) to be applied to the validator
     * @return $this the model itself
     */
    public function addRule($attributes, $validator, $options = []) {
        $validators = $this->getValidators();
        $validators->append(Validator::createValidator($validator, $this, (array) $attributes, $options));

        return $this;
    }

    public function multipleDeleteByIds(array $ids) {
        if (count($ids > 0)) {
//            $condition = ['in', 'id', $ids];
//            $items = static::find()->where(['in', 'id', $ids])->all();
            $items = static::findAll(['id' => $ids]);
            foreach ($items as $item) {
                if (!$item->delete()) {
                    return false;
                }
            }
        }
        return true;
    }

    public function setupDefaultValues() {
        $this->loadDefaultValues();
    }

}
