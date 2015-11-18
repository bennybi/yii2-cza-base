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
class ActiveField extends \yii\widgets\ActiveField {

    public $imageUploadUrl = 'image-upload';
    public $imageListUrl = 'image-list';
    public $fileUploadUrl = 'file-upload';
    public $fileListUrl = 'file-list';

    public function hiddenInput($options = []) {
        $options = array_merge($this->inputOptions, $options);
        $this->adjustLabelFor($options);
        $this->parts['{label}'] = false;
        $this->parts['{input}'] = Html::activeHiddenInput($this->model, $this->attribute, $options);

        return $this;
    }

    /**
     * Renders a text input.
     * This method will generate the "name" and "value" tag attributes automatically for the model attribute
     * unless they are explicitly specified in `$options`.
     * @param array $options the tag options in terms of name-value pairs. These will be rendered as
     * the attributes of the resulting tag. The values will be HTML-encoded using [[Html::encode()]].
     * @return static the field object itself
     */
    public function spinnerInput($options = [], $clientOptions = ['step' => 1, 'min' => 0]) {
        $options = array_merge($this->inputOptions, $options);
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = \yii\jui\Spinner::widget([
                    'model' => $this->model,
                    'attribute' => $this->attribute,
                    'options' => $options,
                    'clientOptions' => $clientOptions,
        ]);

        return $this;
    }

    /**
     * imperavi editor
     * @param type $options
     * @param type $htmlOptions
     * @param type $params - [language]
     * @return \cza\base\widgets\ActiveField
     */
    public function richtext($options = [], $htmlOptions = [], $params = []) {
        $defaults = [
            'lang' => str_replace("-", "_", strtolower(Yii::$app->language)),
            'minHeight' => 150,
            'imageUpload' => Url::to([$this->imageUploadUrl, 'attr' => $this->attribute]),
            'imageManagerJson' => Url::to([$this->imageListUrl, 'attr' => $this->attribute]),
            'fileUpload' => Url::to([$this->fileUploadUrl, 'attr' => $this->attribute]),
            'fileManagerJson' => Url::to([$this->fileListUrl, 'attr' => $this->attribute]),
            'buttonSource' => true,
        ];
        $options = array_merge($defaults, $options);

        $defaults = [
            'id' => isset($params['language']) ? Html::getInputId($this->model, $this->attribute) . "-{$params['language']}" : Html::getInputId($this->model, $this->attribute),
        ];
        $htmlOptions = array_merge($defaults, $htmlOptions);

        $this->adjustLabelFor($options);
        $this->parts['{input}'] = \cza\base\vendor\widgets\imperavi\Widget::widget([
                    'model' => $this->model,
                    'attribute' => $this->attribute,
                    'htmlOptions' => $htmlOptions,
                    'options' => $options,
                    'plugins' => [
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
                    ]
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
//        $options = array_merge($this->inputOptions, $options);
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
