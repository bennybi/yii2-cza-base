<?php

namespace cza\base\modules\Attachments;

use Yii;
use cza\base\modules\Attachments\models\File;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\i18n\PhpMessageSource;

/**
 * refert to https://github.com/Nemmo/yii2-attachments
 */
class Module extends \yii\base\Module {

    protected $_organizer;
    protected $_userDirPath;
    protected $_storePath;
    public $controllerNamespace = 'cza\base\modules\Attachments\controllers';

    public function init() {
        parent::init();

        $this->_organizer = Yii::$app->czaHelper->folderOrganizer;
        $this->defaultRoute = 'file';
    }

    /**
     * generate file path in time
     * @param $fileHash
     * @return string
     */
    public function getStorePath() {
        if (is_null($this->_storePath)) {
            $this->_storePath = $this->_organizer->getUploadStorePath();
        }
        return $this->_storePath;
    }

    /**
     * generate file path in time
     * @param $fileHash
     * @return string
     */
    public function getFileDirPath($fileHash, $entityModel = null) {
        return $this->_organizer->getFullUploadStoreDir($fileHash, $entityModel);
    }

    /**
     * generate logic path in time
     * @param type $fileHash
     * @param type $entityModel
     * @return type
     */
    public function getFileLogicPath($fileHash, $entityModel = null) {
        return $this->_organizer->getUploadLogicPath($fileHash, $entityModel);
    }

    public function getUserDirPath() {
        if (is_null($this->_userDirPath)) {
            $this->_userDirPath = $this->_organizer->getUserTempDirPath();
        }
        return $this->_userDirPath;
    }

}
