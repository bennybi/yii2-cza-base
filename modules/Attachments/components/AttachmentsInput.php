<?php

namespace cza\base\modules\Attachments\components;

use Yii;
use kartik\file\FileInput;
use cza\base\modules\Attachments\models\UploadForm;
use cza\base\modules\Attachments\ModuleTrait;
use yii\base\InvalidConfigException;
use yii\bootstrap\Widget;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\helpers\Url;

class AttachmentsInput extends FileInput {

    use ModuleTrait;

    public $model;
    public $pluginOptions = [];
    public $options = [];

    public function init() {

        if (empty($this->model)) {
            throw new InvalidConfigException("Property {model} cannot be blank");
        }

        if (isset($this->options['multiple']) && $this->options['multiple']) {
            $this->options['name'] = Html::getInputName($this->model, $this->attribute) . '[]';
        }

        $this->pluginOptions = array_replace_recursive([
            'uploadUrl' => Url::toRoute('/attachments/file/upload'),
            'initialPreview' => $this->model->isNewRecord ? [] : $this->model->getInitialPreview($this->attribute),
            'initialPreviewConfig' => $this->model->isNewRecord ? [] : $this->model->getInitialPreviewConfig($this->attribute),
            'uploadAsync' => false,
            'uploadExtraData' => [
                'entityModelClass' => $this->model->className(),
                'attribute' => $this->attribute,
                'id' => $this->model->isNewRecord ? 0 : $this->model->id,
            ]
                ], $this->pluginOptions);

        parent::init();

        $js = " jQuery('#{$this->options['id']}').on('filezoomshow filezoomprev filezoomnext', function(event, params) {
                    var filePreviewImage = params.modal.find( '.file-preview-image' );
                    filePreviewImage.prop('src', filePreviewImage.prop('src').replace( '/t/', '/'));
                });
                 ";

        $this->registerWidgetJs($js);

        // fix zoom overflow css
        $css = ".file-zoom-content{height: 480px;overflow: auto;text-align: center;}";
        $this->getView()->registerCss($css);
    }

}
