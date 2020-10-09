<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace cza\base\widgets\ui\jui;

use Yii;
use yii\base\Component;
use yii\base\ErrorHandler;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\base\Model;
use yii\web\JsExpression;
use yii\helpers\Url;


/**
 * ActiveField represents a form input field within an [[ActiveForm]].
 * 
 * @author Ben Bi <bennybi@qq.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
class ActiveField extends \yii\widgets\ActiveField {

    /**
     * Renders a text input.
     * This method will generate the "name" and "value" tag attributes automatically for the model attribute
     * unless they are explicitly specified in `$options`.
     * @param array $options the tag options in terms of name-value pairs. These will be rendered as
     * the attributes of the resulting tag. The values will be HTML-encoded using [[Html::encode()]].
     * @return static the field object itself
     */
    public function spinnerInput($options = ['style' => 'width: 20px;height: 20px;'], $clientOptions = ['step' => 1, 'min' => 0]) {
        $options = array_replace_recursive($this->inputOptions, $options);
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = \yii\jui\Spinner::widget([
                    'model' => $this->model,
                    'attribute' => $this->attribute,
                    'options' => $options,
                    'clientOptions' => $clientOptions,
        ]);

        return $this;
    }

    public function datePickerInput($options = ['dateFormat' => 'yyyy-MM-dd']) {
        $options = array_replace_recursive($this->inputOptions, $options);
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = \yii\jui\DatePicker::widget([
                    'model' => $this->model,
                    'attribute' => $this->attribute,
                    'dateFormat' => $options['dateFormat'],
        ]);

        return $this;
    }
}
