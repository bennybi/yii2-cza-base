<?php

namespace cza\base\widgets\Album;

use Yii;
use cza\base\widgets\Widget;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;
use cza\base\models\statics\OperationEvent;
use yii\helpers\Url;
use yii\web\View;

/**
 *  Album Widget
 * @author Ben Bi <bennybi@qq.com>
 * @license
 */
class Album extends Widget {

    public $model = null;
    public $listOptions = [];
    public $editUrl;
    public $uploadAction = 'images-upload';
    public $deleteAction = 'images-delete';
    public $sortAction = 'images-sort';

    public function init() {
        parent::init();
        $defaultOptions = [
            'code' => 'album',
            'extensions' => "jpg,gif,png",
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
        AlbumAsset::register($this->view);
    }

    public function run() {
        $this->clientEvents = [
//            OperationEvent::REFRESH => "function(e, params){ //not work
////                $.pjax({url: '" . Url::current() . "',container: '#{$this->model->getPrefixName('images-list-pjax')}'});
//            } ",
            OperationEvent::REFRESH => "function(e, params){
                e.preventDefault();
                $('#{$this->model->getPrefixName('images-list-pjax')} a.refresh').click();
            } ",
            OperationEvent::EDIT => "function(e, params){
                e.preventDefault();
                var editUrl = '{$this->editUrl}' + '?id=' + params.id;
                $('#{$this->model->getPrefixName('album-modal')}').modal('show').find('.modal-content').html('" . Yii::t('app.c2', 'Loading...') . "').load(editUrl);
            } ",
            OperationEvent::DELETE_BY_ID => "function(e, params){
                e.preventDefault();
                $.ajax({url:'" . Url::toRoute($this->deleteAction) . "',data:params}).done(function(result){
                    $('#{$this->model->getPrefixName('images-list-pjax')} a.refresh').click();
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
        $js .= "$('[data-fancybox]').fancybox();\n";
        $js .= "$(document).on('" . OperationEvent::REFRESH . "', function(e, params) {
                    if(params.type && params.type==" . \common\models\c2\statics\EntityAttachmentType::TYPE_IMAGE . "){
                        e.preventDefault();
                        $('#{$this->model->getPrefixName('images-list-pjax')} a.refresh').click();
                    }
                });\n";
        $this->getView()->registerJs($js, View::POS_READY);
    }

}
