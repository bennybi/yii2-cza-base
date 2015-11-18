<?php

namespace cza\base\components\utils;

use Yii;
use yii\helpers\Url;
use yii\base\Exception;

/**
 * service folder organization
 *
 *
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
class FolderOrganizer extends \yii\base\Component {

    protected $_data = [];
    
    public $uploadFolderName = 'uploads';
    public $cachingLayer = 2;

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

        $path = Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR . $this->uploadFolderName;
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
        $path = $this->getUploadPath(true) . DIRECTORY_SEPARATOR . $folderName;
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
