<?php

namespace cza\base\components\utils;

use Yii;
use yii\helpers\Url;
use yii\base\Exception;
use yii\helpers\FileHelper;

/**
 * service folder organization
 *
 *
 * @author Ben Bi <bennybi@qq.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
class FolderOrganizer extends \yii\base\Component {

    protected $_data = [];
    public $uploadFolderName = 'uploads';
    public $cachingLayer = 2;
    public $uploadTempPath;
    public $uploadStorePath;
    public $fullUploadStoreDirPattern = "{uploadDir}{logicPath}{filename}";

    public function init() {
        parent::init();
    }

    public function getUploadTempPath() {
        return \Yii::getAlias($this->uploadTempPath);
    }

    public function getUploadStorePath() {
        return \Yii::getAlias($this->uploadStorePath);
    }

    public function getUploadStoreUrl() {
        if (!isset($this->_data['uploadStoreUrl'])) {
            $this->_data['uploadStoreUrl'] = isset(Yii::$app->params['config']['upload']['accessUrl']) ? Yii::$app->params['config']['upload']['accessUrl'] . '/' . $this->getPathUnderWeb($this->getUploadStorePath()) : Yii::$app->homeUrl . $this->getPathUnderWeb($this->getUploadStorePath());
        }
        return $this->_data['uploadStoreUrl'];
    }

    public function getPathUnderWeb($path) {
        return substr($path, strrpos($path, 'web') + 4);
    }

    public function getSubDirs($fileHash, $depth = 1) {
        $depth = min($depth, 9);
        $path = '';

        for ($i = 0; $i < $depth; $i++) {
            $folder = substr($fileHash, $i * 3, 2);
            $path .= $folder;
            if ($i != $depth - 1)
                $path .= '/';
        }

        return $path;
    }

    public function getUserTempDirPath() {
        \Yii::$app->session->open();

        $userDirPath = $this->getUploadTempPath() . '/' . \Yii::$app->session->id;
        FileHelper::createDirectory($userDirPath);

        \Yii::$app->session->close();

        return $userDirPath . '/';
    }

    public function getFullUploadStoreDir($fileHash, $entityModel = null) {
        $path = strtr($this->fullUploadStoreDirPattern, [
            "{uploadDir}" => $this->getUploadStorePath() . '/',
            "{logicPath}" => $this->getUploadLogicPath($fileHash, $entityModel),
            "{filename}" => "",
        ]);
        if (!\file_exists($path)) {
            FileHelper::createDirectory($path);
        }
        return $path;
    }

    /**
     * return file store path by EntityFile model
     * @param type $model - file model
     * @return string file full path, inculding filename
     */
    public function getFullUploadStorePath($model) {
        $path = strtr($this->fullUploadStoreDirPattern, [
            "{uploadDir}" => $this->getUploadStorePath() . '/',
            "{logicPath}" => $model->getLogicPath(),
            "{filename}" => $model->getHashFileName(),
        ]);
        return $path;
    }

    public function getUploadLogicPath($fileHash, $entityModel = null, $depth = 1) {
        if (isset($this->_data['LOGIC_PATH'][$fileHash])) {
            return $this->_data['LOGIC_PATH'][$fileHash];
        }

        $logicPath = "";
        if (is_null($entityModel)) {
            $logicPath = $this->getSubDirs($fileHash, $depth) . '/';
        } else {
            $logicPath = $entityModel->formName() . '/' . $entityModel->id . '/' . $this->getSubDirs($fileHash, $depth) . '/';
        }
        $this->_data['LOGIC_PATH'][$fileHash] = $logicPath;
        return $this->_data['LOGIC_PATH'][$fileHash];
    }

    /**
     * Return upload path
     * @param type $isAbs return absolute or relative path
     * @param type $createMode
     * @return string
     * @throws Exception
     */
    public function getUploadPath($isAbs = false, $createMode = 0775) {
        if ($isAbs && isset($this->_data['ABS_UPLOAD_PATH'])) {
            return $this->_data['ABS_UPLOAD_PATH'];
        } elseif (!$isAbs && isset($this->_data['REV_UPLOAD_PATH'])) {
            return $this->_data['REV_UPLOAD_PATH'];
        }

        $path = Yii::getAlias('@webroot') . '/' . $this->uploadFolderName;
        if (!@file_exists($path)) {
            if (!@mkdir($path, $createMode, true)) {
                throw new Exception("Cannot create dir: {$path}");
            }
        }
        $this->_data['ABS_UPLOAD_PATH'] = $path;
        $this->_data['REV_UPLOAD_PATH'] = Url::to('@web/' . basename($path));

        return $isAbs ? $this->_data['ABS_UPLOAD_PATH'] : $this->_data['REV_UPLOAD_PATH'];
    }

    /**
     * return cms upload temporary folder
     * @param boolean $isAbs return absolute file path or relative url
     * @param type $createMode
     * @param type $model
     * @return type
     */
    public function getCmsUploadTempPath($isAbs = false, $createMode = 0775, $model = null) {
        return $this->getScalableUploadPath('CmsTemp', 'ABS_CMS_TEMP', 'REV_CMS_TEMP', $isAbs, $createMode, $model);
    }

    public function getEntityUploadPath($isAbs = false, $createMode = 0775, $model = null) {
        return $this->getScalableUploadPath('Entity', 'ABS_ENTITY_UPLOAD', 'REV_ENTITY_UPLOAD', $isAbs, $createMode, $model);
    }

    /**
     * get scalable folder under upload
     * 
     * @param type $defaultName
     * @param type $absKey
     * @param type $revKey 
     * @param type $isAbs
     * @param type $createMode
     * @param type $model
     * @return type
     * @throws Exception
     */
    protected function getScalableUploadPath($defaultName, $absKey, $revKey, $isAbs = false, $createMode = 0775, $model = null) {
        if ($isAbs && isset($this->_data[$absKey])) {
            return $this->_data[$absKey];
        } elseif (!$isAbs && isset($this->_data[$revKey])) {
            return $this->_data[$revKey];
        }

        $folderName = !is_null($model) ? $model->className() : $defaultName;
        $path = $this->getUploadPath(true) . '/' . $folderName;
        if (!@file_exists($path)) {
            if (!@mkdir($path, $createMode, true)) {
                throw new Exception("Cannot create dir: {$path}");
            }
        }
        $this->_data[$absKey] = $path;
        $this->_data[$revKey] = Url::to($this->getUploadPath() . '/' . $folderName);

        return $isAbs ? $this->_data[$absKey] : $this->_data[$revKey];
    }

    /**
     * accroding $fileName, create two layer caching folder for it, to avoid too much file under a folder
     * 
     * @param string $basePath - file store base directory, 'uploads' etc.
     * @param Model $model - entity class
     * @param int $startCharNum - get random char start position or the flename, for plupload filename
     *
     * @return string caching path, looks like "q/c/" etc.
     */
    public function setupCachingPath($basePath, $fileName, $model = null, $startCharNum = 0, $createMode = 0775) {
        $prefixPath = $basePath;
        $cachingPath = "";

        if (!is_null($model)) {
            $cachingPath = preg_replace(array('/EntityFile/', '/EntityImage/'), array('', ''), basename($model->className()));
            $cachingPath.='/' . $model->id;

            $prefixPath.= '/' . $cachingPath;
            if (!file_exists($prefixPath)) {
                if (!@mkdir($prefixPath, $createMode, true)) {
                    throw new Exception("Cannot create dir: {$prefixPath}");
                }
            }
        }

        if (strlen($fileName) > ($startCharNum + $this->cachingLayer)) {
            $dir = '';
            for ($i = 0; $i < $this->cachingLayer; $i++) {
                $dirChar = substr($fileName, ($startCharNum + $i), 1);
                $dir .= '/' . $dirChar;
                $fullPath = $prefixPath . $dir;
                if (!file_exists($fullPath)) {
                    if (!@mkdir($fullPath, $createMode)) {
                        throw new Exception("Cannot create dir: {$fullPath}");
                    }
                }
            }
            $cachingPath .= $dir;
        } else {
            throw new Exception("Filename({$fileName}) is too short to create caching folders!", "404");
        }
        return $cachingPath;
    }

}
