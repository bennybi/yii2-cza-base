<?php

use yii\helpers\Html;
use cza\base\models\statics\OperationEvent;
use yii\widgets\Pjax;
use kartik\widgets\ActiveForm;
use yii\bootstrap\Modal;
use cza\base\widgets\ui\adminlte2\Box;

$theme = $this->theme;
$options = $this->context->options;
$listOptions = $this->context->listOptions;
$model = $this->context->model;
?>

<?php

/**
 * Plupload Widget
 */
echo Html::beginTag('div', $options);

Box::begin([
    'options' => ['id' => $model->getPrefixName('box')],
    'config' => [
        'type' => 'box-warning',
//        'type' => 'box-warning collapsed-box',
        'header' => [
            'title' => Yii::t('app.c2', 'Expend Uploader'),
            'tools' => '{collapse}',
        ],
        'icons' => [
            'remove' => 'times', // fa icon name
            'collapse' => 'plus', // fa icon name
        ]
    ]
]);
ActiveForm::begin();
echo Html::tag('div', '', ['class' => 'error-container']);
echo \cza\base\vendor\widgets\plupload\Plupload::widget([
    'url' => [$this->context->uploadAction],
    'jsUploaderName' => "filesUploader",
    'htmlOptions' => [
        'id' => $model->getPrefixName('files-uploader')
    ],
    'options' => [
        'filters' => [
            'mime_types' => [
                ['title' => 'Files', 'extensions' => $options['extensions']],
//                ['title' => "Files", 'extensions' => "zip"],
            ],
        ],
        'multipart_params' => [
            'entity_id' => $model->id,
        ]
    ],
    'events' => [
        'UploadComplete' => "function(uploader, files){
            jQuery('#" . $options['id'] . "').trigger('" . OperationEvent::REFRESH . "', {});
        }",
//        'BeforeChunkUpload' => "function(uploader, file, post, current, offset){
//             uploader.settings.multipart_params = $.extend(uploader.settings.multipart_params, {filename: file.name});
//        }",
//        'FilesAdded' => 'function(uploader, files){
//            $("#error-container").hide();
//            $("#browse").button("loading");
//            uploader.start();
//        }',
//        'FileUploaded' => "function(uploader, file, response){
//            jQuery('#" . $options['id'] . "').trigger('" . OperationEvent::REFRESH . "', {});
//        }",
        'Error' => 'function (uploader, error) {
            $("#error-container").html(error.message).show();
        }'
    ],
]);
ActiveForm::end();
Box::end();
?>

<?php

/**
 * Filess list Widget
 */
Pjax::begin(['id' => $model->getPrefixName('files-list-pjax'), 'enablePushState' => false]);
echo cza\base\widgets\FilesManager\FilesList::widget([
    'model' => $model,
    'code' => $options['code'],
    'itemView' => $listOptions['itemView'],
    'options' => [
        'id' => $model->getPrefixName('files-list'),
    ],
]);
echo Html::a("Refresh", \yii\helpers\Url::current(), ['id'=>'file-refresh', 'class' => 'refresh', 'style' => "display:none;"]);
Pjax::end();

/**
 * Popup
 */
Modal::begin([
    'id' => $model->getPrefixName('files-modal'),
    'size' => 'modal-lg',
]);
Modal::end();

echo Html::endTag('div');
?>