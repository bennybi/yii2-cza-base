<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace cza\base\behaviors;

use yii\base\InvalidCallException;
use yii\db\BaseActiveRecord;
use yii\behaviors\AttributeBehavior;
use yii\db\Expression;

/**
 * MyCatIdBehavior change id generation method by MyCat id sequence generation mechanism
 *
 * To use TimestampBehavior, insert the following code to your ActiveRecord class:
 *
 * ```php
 * use cza\base\behaviors\MyCatIdBehavior;
 *
 * public function behaviors()
 * {
 *     return [
 *         [
                'class' => MyCatIdBehavior::className(),
                'value' => new Expression("next value for MYCATSEQ_" . strtoupper(static::getTableSchema()->fullName)),
            ],
 *     ];
 * }
 * ```
 * @author Ben Bi <bennybi@qq.com>
 * @since 2.0
 */
class MyCatIdBehavior extends AttributeBehavior {

    /**
     * @var string the attribute that will receive timestamp value
     * Set this property to false if you do not want to record the creation time.
     */
    public $idAttribute = 'id';

    /**
     * @inheritdoc
     *
     * In case, when the value is `null`, the result of the PHP function [time()](http://php.net/manual/en/function.time.php)
     * will be used as value.
     */
    public $value;

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => [$this->idAttribute],
            ];
        }
    }

    /**
     * @inheritdoc
     *
     * In case, when the [[value]] is `null`, the result of the PHP function [time()](http://php.net/manual/en/function.time.php)
     * will be used as value.
     */
    protected function getValue($event) {
        if ($this->value === null) {
            return new Expression("next value for MYCATSEQ_GLOBAL");
        }
        return parent::getValue($event);
    }

}
