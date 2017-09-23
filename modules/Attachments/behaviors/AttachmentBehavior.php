<?php

/**
 * Created by PhpStorm.
 * User: Алимжан
 * Date: 27.01.2015
 * Time: 12:24
 */

namespace cza\base\modules\Attachments\behaviors;

use Yii;
use cza\base\modules\Attachments\ModuleTrait;
use cza\base\models\ActiveRecord;
use yii\base\Behavior;
use yii\base\UnknownPropertyException;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use yii\base\Exception;
use cza\base\models\statics\ImageSize;

class AttachmentBehavior extends Behavior {

    use ModuleTrait;

    protected $_refAttribues = [];
    protected $_organizer;

    /*
     * accept attributes to class map, determine to crud related attachments
     * array, for example:
     * 'photos' => [
      'class' => EntityAttachmentImage::className(),
      'validator' => 'image',
      'enableVersions' => false,
      'rules' => [
      'maxFiles' => 5,
      'extensions' => Yii::$app->params['config']['upload']['imageWhiteExts'],
      'maxSize' => Yii::$app->params['config']['upload']['maxFileSize'],
      ]
      ],
     * 
     */
    public $attributesDefinition = [];

    public function init() {
        parent::init();

        if (empty($this->attributesDefinition)) {
            throw new Exception('{attributesDefinition} is required!');
        }

        foreach ($this->attributesDefinition as $k => $v) {
            $this->_refAttribues[$k] = "";
        }
        $this->_organizer = Yii::$app->czaHelper->folderOrganizer;
    }

    public function events() {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'applyRules',
            ActiveRecord::EVENT_AFTER_INSERT => 'saveUploads',
            ActiveRecord::EVENT_AFTER_UPDATE => 'saveUploads',
            ActiveRecord::EVENT_AFTER_DELETE => 'deleteUploads'
        ];
    }

    public function applyRules() {
        $refAttributes = array_keys($this->_refAttribues);
        foreach ($refAttributes as $refAttribute) {
            $this->owner->addRule($refAttribute, $this->attributesDefinition[$refAttribute]['validator'], $this->attributesDefinition[$refAttribute]['rules']);
            $this->owner->addRule($refAttribute, $this->attributesDefinition[$refAttribute]['validator'], array_replace_recursive(['on' => ActiveRecord::SCENARIO_UPLOAD], $this->attributesDefinition[$refAttribute]['rules']));
        }
    }

    public function saveUploads($event) {
        $refAttributes = array_keys($this->_refAttribues);
        foreach ($refAttributes as $refAttribute) {
            $name = Html::getInputName($this->owner, $refAttribute);
            $files = UploadedFile::getInstancesByName($name);
            $userTempDir = $this->getModule()->getUserDirPath();
            if (!empty($files)) {
                $this->deleteByRefAttribute($refAttribute);

                foreach ($files as $file) {
                    if (!$file->saveAs($userTempDir . $file->name)) {
                        throw new \Exception(\Yii::t('yii', 'File upload failed.'));
                    }
                }
            }

            if (\file_exists($userTempDir)) {
                foreach (FileHelper::findFiles($userTempDir) as $file) {
                    if (!$this->attachFile($file, $refAttribute)) {
                        throw new \Exception(\Yii::t('yii', 'File upload failed.'));
                    }
                }
            }
        }
        /* cannot delete tmp dir, bcz it will affect realted model's images generation */
//        if (\file_exists($userTempDir)) {
//            \rmdir($userTempDir);
//        }
    }

    public function deleteByRefAttribute($refAttribute) {
        foreach ($this->getFiles($refAttribute) as $file) {
            $file->delete();
        }
    }

    public function deleteUploads($event) {
        $refAttributes = array_keys($this->_refAttribues);
        foreach ($refAttributes as $refAttribute) {
            $this->deleteByRefAttribute($refAttribute);
        }
    }

    /**
     * @param $filePath string
     * @param $owner
     * @return bool|File
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function attachFile($filePath, $attribute, $extras = []) {
        $owner = $this->owner;
        if (empty($owner->id)) {
            throw new Exception('Parent model must have ID when you attaching a file');
        }
        if (!\file_exists($filePath)) {
            throw new Exception("File {$filePath} not exists");
        }

        $fileHash = md5(microtime(true) . $filePath);
        $fileType = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $newFileName = "{$fileHash}.{$fileType}";

        $fileDirPath = $this->getModule()->getFileDirPath($fileHash, $owner);
        $newFilePath = $fileDirPath . DIRECTORY_SEPARATOR . $newFileName;

        if (!copy($filePath, $newFilePath)) {
            throw new Exception("Cannot copy file! {$filePath}  to {$newFilePath}");
        }
        $file = new $this->attributesDefinition[$attribute]['class'];
        if (isset($this->attributesDefinition[$attribute]['enableVersions'])) {
            $file->setEnableVersions($this->attributesDefinition[$attribute]['enableVersions']);
        }

        $file->loadDefaultValues();
        $file->setAttributes([
            'name' => pathinfo($filePath, PATHINFO_FILENAME),
            'entity_id' => $owner->id,
            'entity_class' => $owner->className(),
            'entity_attribute' => $attribute,
            'hash' => $fileHash,
            'size' => filesize($filePath),
            'content' => isset($extras['content']) ? $extras['content'] : "",
            'extension' => $fileType,
            'mime_type' => FileHelper::getMimeType($filePath),
            'logic_path' => $this->getModule()->getFileLogicPath($fileHash, $owner),
        ]);

        if ($file->save()) {
            @\unlink($filePath);
            return $file;
        } else {
            return false;
        }
    }

    /**
     * @return File[]
     * @throws \Exception
     */
    public function getFiles($attribute, $order = ['id' => SORT_ASC]) {
        $modelClass = $this->attributesDefinition[$attribute]['class'];
        $fileQuery = $modelClass::find()
                ->andWhere([
            'entity_id' => $this->owner->id,
            'entity_class' => $this->owner->className(),
            'entity_attribute' => $attribute,
        ]);
        $fileQuery->orderBy($order);

        return $fileQuery->all();
    }

    public function getOneAttachment($attribute, $order = ['position' => SORT_DESC]) {
        $modelClass = $this->attributesDefinition[$attribute]['class'];
        $fileQuery = $modelClass::find()
                ->andWhere([
            'entity_id' => $this->owner->id,
            'entity_class' => $this->owner->className(),
            'entity_attribute' => $attribute,
        ]);
        $fileQuery->orderBy($order);
        return $fileQuery->one();
    }

    public function getInitialPreview($attribute, $imageFormat = ImageSize::THUMBNAIL) {
        $initialPreview = [];

        $userTempDir = $this->getModule()->getUserDirPath();
        if (file_exists($userTempDir)) {
            foreach (FileHelper::findFiles($userTempDir) as $file) {
                if (substr(FileHelper::getMimeType($file), 0, 5) === 'image') {
                    $initialPreview[] = Html::img(['/attachments/file/download-temp', 'filename' => basename($file)], ['class' => 'file-preview-image kv-preview-data', 'style' => 'width:auto;height:160px;']);
                } else {
                    $initialPreview[] = Html::beginTag('div', ['class' => 'file-preview-other']) .
                            Html::beginTag('h2') .
                            Html::tag('i', '', ['class' => 'glyphicon glyphicon-file']) .
                            Html::endTag('h2') .
                            Html::endTag('div');
                }
            }
        }

        foreach ($this->getFiles($attribute) as $file) {
            if (substr($file->mime_type, 0, 5) === 'image') {
                $initialPreview[] = Html::img($file->getUrlByFormat($imageFormat), ['class' => 'file-preview-image kv-preview-data', 'style' => 'width:190px;height:auto;']);
            } else {
                $initialPreview[] = Html::beginTag('div', ['class' => 'file-preview-other']) .
                        Html::beginTag('h2') .
                        Html::a(Html::tag('i', '', ['class' => 'glyphicon glyphicon-file']), $file->getDownloadUrl()) .
                        Html::endTag('h2') .
                        Html::endTag('div');
            }
        }

        return $initialPreview;
    }

    public function getInitialPreviewConfig($attribute) {
        $initialPreviewConfig = [];

        $userTempDir = $this->getModule()->getUserDirPath();
        if (file_exists($userTempDir)) {
            foreach (FileHelper::findFiles($userTempDir) as $file) {
                $filename = basename($file);
                $initialPreviewConfig[] = [
                    'caption' => $filename,
                    'size' => \filesize($file),
                    'url' => Url::to(['/attachments/file/delete-temp', 'filename' => $filename]),
                ];
            }
        }

        foreach ($this->getFiles($attribute) as $index => $file) {
            $initialPreviewConfig[] = [
                'caption' => $file->getFileName(),
                'size' => $file->size,
                'url' => Url::toRoute(['/attachments/file/delete', 'id' => $file->id]),
            ];
        }

        return $initialPreviewConfig;
    }

    /**
     * PHP getter magic method.
     * This method is overridden so that relation attribute can be accessed like property.
     *
     * @param string $name property name
     * @throws UnknownPropertyException if the property is not defined
     * @return mixed property value
     */
    public function __get($name) {
        try {
            return parent::__get($name);
        } catch (UnknownPropertyException $exception) {
            if (array_key_exists($name, $this->_refAttribues)) {
                return $this->_refAttribues[$name];
            }
            throw $exception;
        }
    }

    /**
     * PHP setter magic method.
     * This method is overridden so that relation attribute can be accessed like property.
     * @param string $name property name
     * @param mixed $value property value
     * @throws UnknownPropertyException if the property is not defined
     */
    public function __set($name, $value) {
        try {
            parent::__set($name, $value);
        } catch (UnknownPropertyException $exception) {
            if (array_key_exists($name, $this->_refAttribues)) {
                $this->_refAttribues[$name] = $value;
            } else {
                throw $exception;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function canGetProperty($name, $checkVars = true) {
        if (parent::canGetProperty($name, $checkVars)) {
            return true;
        }
        return (array_key_exists($name, $this->_refAttribues));
    }

    /**
     * @inheritdoc
     */
    public function canSetProperty($name, $checkVars = true) {
        if (parent::canSetProperty($name, $checkVars)) {
            return true;
        }
        return (array_key_exists($name, $this->_refAttribues));
    }

    public function getAttachmentAttributes() {
        return array_keys($this->attributesDefinition);
    }

}
