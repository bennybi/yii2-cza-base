<?php

namespace cza\base\components\actions\backend;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\FileHelper;
use yii\helpers\Url;

/**
 * Descriptions
 *
 *
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
class CmsFileListAction extends \yii\rest\Action {

    /**
     * @var callable a PHP callable that will be called to prepare a data provider that
     * should return a collection of the models. If not set, [[prepareDataProvider()]] will be used instead.
     * The signature of the callable should be:
     *
     * ```php
     * function ($action) {
     *     // $action is the action object currently running
     * }
     * ```
     *
     * The callable should return an instance of [[ActiveDataProvider]].
     */
    public $prepareDataProvider;
    public $uploadPath;
    public $uploadUrl;
    public $whiteExts = ['gif', 'png', 'jpg', 'jpeg'];
    public $blackExts = [];

    /**
     * @return array
     */
    public function run() {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        $params = Yii::$app->request->getQueryParams();
        return $this->prepareDataProvider($params);
    }

    /**
     * Prepares the data provider that should return the requested collection of the models.
     * @return array
     */
    protected function prepareDataProvider($params = []) {
        if (!isset($params['attr']))
            throw new Exception('Require parameter "attr"!');

        $prefix = is_null($this->controller->module) ? $this->controller->module->uniqueId : $this->controller->module->id;
        $prefix.= DIRECTORY_SEPARATOR . $params['attr'];
        $uploadPath = $this->uploadPath . DIRECTORY_SEPARATOR . $prefix;
        $uploadUrl = str_replace('\\', '/', $this->uploadUrl . '/' . $prefix);

        $data = [];
        if (@file_exists($uploadPath)) {
            $whiteExts = array_map(function($value) {
                return "*.{$value}";
            }, $this->whiteExts);
            $files = FileHelper::findFiles($uploadPath, ['only' => $whiteExts]);

            foreach ($files as $file) {
                $fileName = basename($file);
                $data[] = [
                    'title' => $fileName,
                    'thumb' => Url::to($uploadUrl . '/' . $fileName),
                    'image' => Url::to($uploadUrl . '/' . $fileName),
                ];
            }
        }

        return $data;
    }

}
