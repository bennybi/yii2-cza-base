<?php

namespace cza\base\widgets\FilesManager;

use Yii;
use cza\base\widgets\Widget;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

/**
 *  FilesList Widget
 * @author Ben Bi <bennybi@qq.com>
 * @license
 */
class FilesList extends Widget {

    public $model = null;
    public $code = 'files';
    public $dataProvider = null;
    public $itemOptions = ['class' => 'grid-item'];
    public $itemView = '_list_item';
    public $viewParams = [];
    public $pageSize = 50;

    public function init() {
        parent::init();
        $defaultOptions = [];
        $this->options = ArrayHelper::merge($defaultOptions, $this->options);

        if (is_null($this->model)) {
            throw new \yii\base\Exception('Model parameter is required!');
        }
    }

    public function run() {

        $this->dataProvider = new ActiveDataProvider([
            'query' => $this->model->getAttachementFiles($this->code),
            'pagination' => [
                'pageSize' => $this->pageSize,
                'params' => Yii::$app->request->get(),
            ],
        ]);


        $attachments = $this->dataProvider->getModels();
        $ids = $this->dataProvider->getKeys();
        $items = [];
        foreach (array_values($attachments) as $index => $model) {
            $key = $ids[$index];
            $items[$key] = $this->renderItem($model, $key, $index);
        }

        return $this->render($this->template, ['items' => $items, 'ids' => $ids]);
    }

    public function renderItem($model, $key, $index) {
        $item = [
            'options' => $this->itemOptions, ['class' => 'grid-item'],
            'content' => $this->getView()->render($this->itemView, array_merge([
                'model' => $model,
                'key' => $key,
                'index' => $index,
                'widget' => $this,], $this->viewParams))
        ];
        return $item;
    }

}
