<?php

namespace cza\base\components\actions\backend;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\UploadedFile;
use yii\web\HttpException;
use yii\base\Model;
use yii\base\Exception;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * Descriptions
 *
 *
 * @author Ben Bi <bennybi@qq.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
class CmsFileUploadAction extends \yii\rest\Action {

    /**
     * @var string the scenario to be assigned to the new model before it is validated and saved.
     */
    public $scenario = Model::SCENARIO_DEFAULT;
    public $uploadPath;
    public $uploadUrl;
    public $uploadCreate = false;
    public $createMode = 0775;
    public $whiteExts = ['zip', 'pdf', 'doc', 'docx', 'xlsx', 'txt'];
    public $blackExts = ['php', 'sh', 'pl'];

    /**
     * @return array
     */
    public function run() {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        $params = Yii::$app->request->getQueryParams();
        if (!isset($params['attr']))
            throw new Exception('Require parameter "attr"!');

        $prefix = is_null($this->controller->module) ? $this->controller->module->uniqueId : $this->controller->module->id;
        $prefix.= DIRECTORY_SEPARATOR . $params['attr'];
        $uploadPath = $this->uploadPath . DIRECTORY_SEPARATOR . $prefix;
        $uploadUrl = str_replace('\\', '/', $this->uploadUrl . '/' . $prefix);

        if (!@file_exists($uploadPath) && $this->uploadCreate) {
            if (!@mkdir($uploadPath, $this->createMode, true)) {
                throw new HttpException(500, Json::encode(
                        ['error' => 'Could not create upload folder "' . $uploadPath . '".']
                ));
            }
        }

        $file = UploadedFile::getInstanceByName('file');

        if ($file instanceof UploadedFile) {
            $extension = strtolower($file->getExtension());
            if (!in_array($extension, $this->whiteExts) || in_array($extension, $this->blackExts)) {
                throw new Exception('Invalid file extension: ' . $extension . '.');
            }
            $fileName = trim($this->generateName($params['attr'])) . '.' . $extension;
            $filePath = $uploadPath . DIRECTORY_SEPARATOR . $fileName;
            while (file_exists($filePath)) {
                $fileName = trim($this->generateName($params['attr'])) . '.' . $extension;
                $filePath = $uploadPath . DIRECTORY_SEPARATOR . $fileName;
            }

            if (!$file->saveAs($filePath)) {
                throw new HttpException(500, Json::encode(
                        ['error' => 'Could not save file or file exists: "' . $filePath . '".']
                ));
            }

            $attributeUrl = $uploadUrl . '/' . $fileName;
            $data = [ 'filelink' => $attributeUrl,];
            return Json::encode($data);
        } else {
            throw new HttpException(500, Json::encode(
                    ['error' => 'Could not upload file.']
            ));
        }
    }

    public function generateName($source) {
        return md5($source . time() . uniqid(rand(), true));
    }

}
