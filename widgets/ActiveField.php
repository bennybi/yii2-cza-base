<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace cza\base\widgets;

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
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
class ActiveField extends \yii\bootstrap\ActiveField {

    /**
     *
     * @var array, for multiple attributes assigment
     */
    public $attributes;
    public $imageUploadUrl = 'image-upload';
    public $imageListUrl = 'image-list';
    public $fileUploadUrl = 'file-upload';
    public $fileListUrl = 'file-list';

    public function emailInput($options = [], $tpl = '<div class="input-group"><span class="input-group-addon"><i class="fa fa-envelope"></i></span>{input}</div>') {
        $this->inputTemplate = $tpl;
        return parent::textInput($options);
    }

    public function select2Input($data, $options = [], $pluginOptions = [], $toggleAllSettings = []) {
        $defaults = [
            'model' => $this->model,
            'attribute' => $this->attribute,
            'data' => $data,
            'options' => [
                'multiple' => true,
                'placeholder' => Yii::t('app.c2', 'Select options ..'),
            ],
            'pluginOptions' => [
                'tags' => true,
            ],
            'toggleAllSettings' => [
                'selectLabel' => '<i class="glyphicon glyphicon-unchecked"></i>' . Yii::t('app.c2', 'Select all'),
                'unselectLabel' => '<i class="glyphicon glyphicon-check"></i>' . Yii::t('app.c2', 'Unselect all'),
            ],
        ];
        $defaults['options'] = array_replace_recursive($defaults['options'], $options);
        $defaults['pluginOptions'] = array_replace_recursive($defaults['pluginOptions'], $pluginOptions);
        $defaults['toggleAllSettings'] = array_replace_recursive($defaults['toggleAllSettings'], $toggleAllSettings);

        $this->parts['{input}'] = \kartik\select2\Select2::widget($defaults);

        return $this;
    }

    /**
     * override original method
     * @param type $options
     * @return \cza\base\widgets\ActiveField
     */
    public function hiddenInput($options = []) {
        $options = array_replace_recursive($this->inputOptions, $options);
        $this->adjustLabelFor($options);
        $this->parts['{label}'] = false;
        $this->parts['{input}'] = Html::activeHiddenInput($this->model, $this->attribute, $options);

        return $this;
    }

    /**
     * 
     * @param type $options
     * @param type $pluginOptions
     * @param type $pluginEvents
     * @return \cza\base\widgets\ActiveField
     */
    public function spinnerInput($options = [], $pluginOptions = [], $pluginEvents = []) {
        $defaults = [
            'disabled' => false,
            'readonly' => false,
            'htmlOptions' => array_replace_recursive(['style' => "text-align:center;"], $this->inputOptions),
        ];
        $options = array_replace_recursive($defaults, $options);

        $defaults = [
            'buttondown_txt' => '<i class="glyphicon glyphicon-minus-sign"></i>',
            'buttonup_txt' => '<i class="glyphicon glyphicon-plus-sign"></i>',
        ];
        $pluginOptions = array_replace_recursive($defaults, $pluginOptions);

        $this->adjustLabelFor($options);
        $this->parts['{input}'] = \kartik\touchspin\TouchSpin::widget([
                    'model' => $this->model,
                    'attribute' => $this->attribute,
                    'disabled' => $options['disabled'],
                    'readonly' => $options['readonly'],
                    'options' => $options['htmlOptions'],
                    'pluginOptions' => $pluginOptions,
                    'pluginEvents' => $pluginEvents,
                        ]
        );

        return $this;
    }

    /**
     * refert to http://demos.krajee.com/widget-details/datepicker
     * @param type $options
     * @param type $pluginOptions
     * @param type $pluginEvents
     * @return \cza\base\widgets\ActiveField
     */
    public function datePickerInput($options = [], $pluginOptions = [], $pluginEvents = []) {
        $defaults = [
            'htmlOptions' => $this->inputOptions,
        ];
        $options = array_replace_recursive($defaults, $options);

        $defaults = [
            'autoclose' => true,
            'todayHighlight' => true,
            'format' => 'yyyy-mm-dd',
        ];
        $pluginOptions = array_replace_recursive($defaults, $pluginOptions);

        $this->adjustLabelFor($options);
        $this->parts['{input}'] = \kartik\widgets\DatePicker::widget([
                    'model' => $this->model,
                    'attribute' => $this->attribute,
                    'pluginOptions' => $pluginOptions,
                    'options' => $options['htmlOptions'],
        ]);

        return $this;
    }

    public function dateRangePickerInput($options = [], $options2 = [], $pluginOptions = [], $pluginEvents = []) {
        $defaults = [
            'options' => $this->inputOptions,
            'options2' => $this->inputOptions,
            'separator' => '<i class="glyphicon glyphicon-resize-horizontal"></i>',
            'layout' => "<span class='input-group-addon'>" . $this->model->getAttributeLabel($this->attributes[0]) . "</span>{input1}{separator}<span class='input-group-addon'>" . $this->model->getAttributeLabel($this->attributes[1]) . "</span>{input2}<span class='input-group-addon kv-date-remove'><i class='glyphicon glyphicon-remove'></i></span>",
        ];
        $options = array_replace_recursive($defaults, $options);

        $defaults = [
            'autoclose' => true,
            'todayHighlight' => true,
            'format' => 'yyyy-mm-dd',
        ];
        $pluginOptions = array_replace_recursive($defaults, $pluginOptions);

        return \kartik\widgets\DatePicker::widget([
                    'model' => $this->model,
                    'type' => \kartik\widgets\DatePicker::TYPE_RANGE,
                    'attribute' => $this->attributes[0],
                    'attribute2' => $this->attributes[1],
                    'pluginOptions' => $pluginOptions,
                    'separator' => $options['separator'],
                    'layout' => $options['layout'],
                    'options' => $options['options'],
                    'options2' => $options['options2'],
        ]);
    }

    /**
     * imperavi editor
     * @param type $options
     * @param type $htmlOptions
     * @param type $params - [language]
     * @return \cza\base\widgets\ActiveField
     */
    public function richtextInput($options = [], $pluginOptions = [], $htmlOptions = []) {
        $defaults = [
            'fontsize',
            'fontfamily',
            'fontcolor',
            'table',
            'textdirection',
            'video',
            //                        'textexpander',
            //                        'limiter',
            'filemanager',
            'imagemanager',
            //                        'clips',
            'fullscreen',
        ];
        $pluginOptions = array_replace_recursive($defaults, $pluginOptions);

        $defaults = [
            'minHeight' => 150,
            'imageUpload' => Url::to([$this->imageUploadUrl, 'attr' => $this->attribute]),
            'imageManagerJson' => Url::to([$this->imageListUrl, 'attr' => $this->attribute]),
            'fileUpload' => Url::to([$this->fileUploadUrl, 'attr' => $this->attribute]),
            'fileManagerJson' => Url::to([$this->fileListUrl, 'attr' => $this->attribute]),
            'buttonSource' => true,
            'lang' => \Yii::$app->czaHelper->getRegularLangName(),
            'plugins' => $pluginOptions,
        ];
        $options = array_replace_recursive($defaults, $options);

        $defaults = [
            'id' => isset($params['language']) ? Html::getInputId($this->model, $this->attribute) . "-{$params['language']}" : Html::getInputId($this->model, $this->attribute),
        ];
        $htmlOptions = array_replace_recursive($defaults, $htmlOptions);

        $this->adjustLabelFor($options);
        $this->parts['{input}'] = \vova07\imperavi\Widget::widget([
                    'model' => $this->model,
                    'attribute' => $this->attribute,
//                    'htmlOptions' => $htmlOptions,
                    'settings' => $options,
        ]);

        return $this;
    }

    /**
     * TinyMce editor
     * Renders a richtext area.
     * The model attribute value will be used as the content in the textarea.
     * @param array $options the tag options in terms of name-value pairs. These will be rendered as
     * the attributes of the resulting tag. The values will be HTML-encoded using [[Html::encode()]].
     * @return static the field object itself
     */
//    public function richtext($options = ['rows' => 6]) {
//        $options = array_replace_recursive($this->inputOptions, $options);
//        $this->adjustLabelFor($options);
//        $this->parts['{input}'] = \dosamigos\tinymce\TinyMce::widget([
//                    'model' => $this->model,
//                    'attribute' => $this->attribute,
//                    'language' => str_replace("-", "_", Yii::$app->language),
//                    'options' => $options,
//                    'clientOptions' => [
//                        'plugins' => [
//                            "advlist autolink lists link image charmap print preview hr anchor pagebreak",
//                            "searchreplace wordcount visualblocks visualchars code fullscreen",
//                            "insertdatetime media nonbreaking save table contextmenu directionality",
//                            "emoticons template paste textcolor"
//                        ],
//                        'toolbar' => "insertfile undo redo | styleselect | forecolor backcolor emoticons | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
//                        'image_advtab' => true
//                    ]
////                    'clientOptions' => [
////                        'plugins' => [
////                            "advlist autolink lists link image charmap print preview anchor",
////                            "searchreplace visualblocks code fullscreen",
////                            "insertdatetime media table contextmenu paste"
////                        ],
////                        'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
////                        'image_advtab' => true
////                    ]
//        ]);
//
//        return $this;
//    }


    /**
     * ckeditor editor
     * @param type $options
     * @return \cza\base\widgets\ActiveField
     */
//    public function richtext($options = []) {
//        $this->adjustLabelFor($options);
//        $this->parts['{input}'] = \dosamigos\ckeditor\CKEditor::widget([
//                    'model' => $this->model,
//                    'attribute' => $this->attribute,
//                    'options' => $options,
//                    'preset' => 'basic',
//        ]);
//
//        return $this;
//    }
}
