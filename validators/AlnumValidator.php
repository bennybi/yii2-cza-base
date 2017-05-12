<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace cza\base\validators;

use Yii;
use yii\validators\ValidationAsset;
use yii\validators\StringValidator;

/**
 * StringValidator validates that the attribute value is of certain length.
 *
 * Note, this validator should only be used with string-typed attributes.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AlnumValidator extends StringValidator {

    /**
     * @var string user-defined error message used when the length of the value is not equal to [[length]].
     */
    public $notAlnum;

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        if ($this->notAlnum === null) {
            $this->notAlnum = Yii::t('cza', '{attribute} must be alphanumeric.');
        }
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute) {
        $value = $model->$attribute;

        if (!is_string($value)) {
            $this->addError($model, $attribute, $this->message);

            return;
        }

        $length = mb_strlen($value, $this->encoding);

        if ($this->min !== null && $length < $this->min) {
            $this->addError($model, $attribute, $this->tooShort, ['min' => $this->min]);
        }
        if ($this->max !== null && $length > $this->max) {
            $this->addError($model, $attribute, $this->tooLong, ['max' => $this->max]);
        }
        if ($this->length !== null && $length !== $this->length) {
            $this->addError($model, $attribute, $this->notEqual, ['length' => $this->length]);
        }
        if (!ctype_alnum($value)) {
            $this->addError($model, $attribute, $this->notAlnum, []);
        }
    }

    /**
     * @inheritdoc
     */
    protected function validateValue($value) {
        if (!is_string($value)) {
            return [$this->message, []];
        }

        $length = mb_strlen($value, $this->encoding);

        if ($this->min !== null && $length < $this->min) {
            return [$this->tooShort, ['min' => $this->min]];
        }
        if ($this->max !== null && $length > $this->max) {
            return [$this->tooLong, ['max' => $this->max]];
        }
        if ($this->length !== null && $length !== $this->length) {
            return [$this->notEqual, ['length' => $this->length]];
        }
        if (!ctype_alnum($value)) {
            return [$this->notAlnum, []];
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function clientValidateAttribute($model, $attribute, $view) {
        ValidationAsset::register($view);
        $options = $this->getClientOptions($model, $attribute);

        return 'yii.validation.string(value, messages, ' . json_encode($options, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ');';
    }

    public function getClientOptions($model, $attribute) {
        $label = $model->getAttributeLabel($attribute);

        $options = [
            'message' => Yii::$app->getI18n()->format($this->message, [
                'attribute' => $label,
                    ], Yii::$app->language),
        ];

        if ($this->min !== null) {
            $options['min'] = $this->min;
            $options['tooShort'] = Yii::$app->getI18n()->format($this->tooShort, [
                'attribute' => $label,
                'min' => $this->min,
                    ], Yii::$app->language);
        }
        if ($this->max !== null) {
            $options['max'] = $this->max;
            $options['tooLong'] = Yii::$app->getI18n()->format($this->tooLong, [
                'attribute' => $label,
                'max' => $this->max,
                    ], Yii::$app->language);
        }
        if ($this->length !== null) {
            $options['is'] = $this->length;
            $options['notEqual'] = Yii::$app->getI18n()->format($this->notEqual, [
                'attribute' => $label,
                'length' => $this->length,
                    ], Yii::$app->language);
        }
        if ($this->skipOnEmpty) {
            $options['skipOnEmpty'] = 1;
        }

        return $options;
    }

}
