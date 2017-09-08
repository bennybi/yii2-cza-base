<?php

namespace cza\base\widgets\FilesManager;

use Yii;
use cza\base\widgets\Widget;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;
use cza\base\models\statics\OperationEvent;
use yii\helpers\Url;
use yii\web\View;

/**
 *  FilesManager Widget
 * @author Ben Bi <bennybi@qq.com>
 * @license
 */
class FilesManager extends Widget {

    public $model = null;
    public $listOptions = [];
    public $editUrl;
    public $uploadAction = 'files-upload';
    public $deleteAction = 'files-delete';
    public $sortAction = 'files-sort';

    public function init() {
        parent::init();
        $defaultOptions = [
            'code' => 'files',
            'extensions' => "jpg,gif,png,zip",
        ];
        $this->options = ArrayHelper::merge($defaultOptions, $this->options);

        $defaultOptions = [
            'itemView' => '_list_item',
        ];
        $this->listOptions = ArrayHelper::merge($defaultOptions, $this->listOptions);

        if (empty($this->model)) {
            throw new \yii\base\Exception('model parameter is required!');
        }
        if (empty($this->editUrl)) {
            throw new \yii\base\Exception('editUrl parameter is required!');
        }
    }

    public function run() {
        $this->clientEvents = [
//            OperationEvent::REFRESH => "function(e, params){ //not work
////                $.pjax({url: '" . Url::current() . "',container: '#{$this->model->getPrefixName('files-list-pjax')}'});
//            } ",
            OperationEvent::REFRESH => "function(e, params){
                e.preventDefault();
                $('#{$this->model->getPrefixName('files-list-pjax')} a.refresh').click();
            } ",
            OperationEvent::EDIT => "function(e, params){
                e.preventDefault();
                var editUrl = '{$this->editUrl}' + '?id=' + params.id;
                $('#{$this->model->getPrefixName('files-modal')}').modal('show').find('.modal-content').html('" . Yii::t('app.c2', 'Loading...') . "').load(editUrl);
            } ",
            OperationEvent::DELETE_BY_ID => "function(e, params){
                e.preventDefault();
                $.ajax({url:'" . Url::toRoute($this->deleteAction) . "',data:params}).done(function(result){
                    $('#{$this->model->getPrefixName('files-list-pjax')} a.refresh').click();
                }).fail(function(result){alert(result);});
            } ",
            OperationEvent::SORT => "function(e, params){
                e.preventDefault();
                $.ajax({url:'" . Url::toRoute($this->sortAction) . "',data:params}).done(function(result){}).fail(function(result){alert(result);});
            } ",
        ];

        $this->registerClientEvents();
        $this->registerJs();
        return parent::run();
    }

    public function registerJs() {
        $js = "";
        $js .= "$(document).on('" . OperationEvent::REFRESH . "', function(e, params) {
                    if(params.type && params.type==" . \common\models\c2\statics\EntityAttachmentType::TYPE_FILE . "){
                        e.preventDefault();
                        $('#{$this->model->getPrefixName('files-list-pjax')} a.refresh').click();
                    }
                });\n";
        $this->getView()->registerJs($js, View::POS_READY);
    }

}
