<?php

/**
 *  AttachmentBehavior
 * @author Ben Bi <bennybi@qq.com>
 * @license
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

/**
 * 附件行为
 * example:
 * public function behaviors() {
  return ArrayHelper::merge(parent::behaviors(), [
  // handle attachments
  'attachmentsBehavior' => [
  'class' => AttachmentBehavior::class,
  'config' => [
  'entity_class' => static::class,
  'delSrcFileAfterAttach' => false, // 附件后不删除源文件
  ],
  'attributesDefinition' => [
  'avatar' => [
  'class' => EntityAttachmentImage::class,
  'validator' => 'image',
  'enableVersions' => true, // determine to generate difference size images
  'rules' => [
  'maxFiles' => 1,
  'extensions' => Yii::$app->params['config']['upload']['imageWhiteExts'],
  'maxSize' => Yii::$app->params['config']['upload']['maxFileSize'],
  ]
  ],
  ],
  ],
  ]);
  }
 */
class AttachmentBehavior extends Behavior {

    use ModuleTrait;

    protected $_refAttribues = [];
    protected $_organizer;
    public $dirMode = 0775;
    public $entityIdAttribute = 'id';
    public $tempPath = '@runtime/uploads/temp';
    public $config = [];
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

        $this->config = ArrayHelper::merge([
                    'entity_class' => '',
                    'delSrcFileAfterAttach' => true,
                        ], $this->config);

        if (empty($this->attributesDefinition)) {
            throw new Exception('{attributesDefinition} is required!');
        }

        foreach ($this->attributesDefinition as $k => $v) {
            $this->_refAttribues[$k] = "";
        }
        $this->_organizer = Yii::$app->czaHelper->folderOrganizer;
        if (!is_dir($this->tempPath)) {
            FileHelper::createDirectory($this->tempPath, $this->dirMode, true);
        }
    }

    public function events() {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'applyRules',
            ActiveRecord::EVENT_AFTER_INSERT => 'saveUploads',
            ActiveRecord::EVENT_AFTER_UPDATE => 'saveUploads',
            ActiveRecord::EVENT_AFTER_DELETE => 'deleteUploads'
        ];
    }

    /**
     * Base64附件
     * @param $stream string
     * @param $owner
     * @return bool|File
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function attachFileBase64($stream, $attribute, $extras = []) {
        $owner = $this->owner;
        $idAttribute = $this->entityIdAttribute;
        if (empty($owner->$idAttribute)) {
            throw new Exception('Parent model must have ID when you attaching a file');
        }

        $uploadedFile = $this->makeTempFile($stream);
        $filePath = $uploadedFile->tempName;
        if (!\file_exists($filePath)) {
            throw new Exception("File {$filePath} not exists");
        }
        $fileHash = md5(microtime(true) . $filePath);
        $fileType = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $newFileName = "{$fileHash}.{$fileType}";
        $newFilePath = $this->_organizer->getFullUploadStoreDir($newFileName, $owner, 1, $this->entityIdAttribute) . '/' . $newFileName;

        if (!copy($filePath, $newFilePath)) {
            throw new Exception("Cannot copy file! {$filePath}  to {$newFilePath}");
        }
        $file = new $this->attributesDefinition[$attribute]['class'];
        if (isset($this->attributesDefinition[$attribute]['enableVersions'])) {
            $file->setEnableVersions($this->attributesDefinition[$attribute]['enableVersions']);
        }

        $file->loadDefaultValues();
        $file->setAttributes([
            'entity_id' => $owner->$idAttribute,
            'entity_class' => $this->getEntityClass(),
            'entity_attribute' => $attribute,
            'hash' => $fileHash,
            'size' => filesize($newFilePath),
            'name' => $extras['name'] ?? $newFileName,
            'label' => $extras['label'] ?? "",
            'content' => $extras['content'] ?? "",
            'extension' => $fileType,
            'mime_type' => FileHelper::getMimeType($newFilePath),
            'logic_path' => $this->_organizer->getUploadLogicPath($fileHash, $owner, 1, $this->entityIdAttribute),
        ]);

        if ($file->save()) {
            if ($this->config['delSrcFileAfterAttach']) {
                @\unlink($filePath);
            }
            return $file;
        } else {
            return false;
        }
    }

    protected function makeTempFile($attribute) {
        $tempName = tempnam($this->tempPath, 'ub_');
        if (preg_match('/^data:([\w\/]+);base64/i', $attribute, $matches)) {
            list($type, $data) = explode(';', $attribute);
            list(, $data) = explode(',', $attribute);
            $data = base64_decode($data);

            $newName = $tempName;
            $name = basename($tempName);

            if (!empty($matches[1])) {
                $extensions = FileHelper::getExtensionsByMimeType($matches[1]);
                $name .= '.' . end($extensions);
                $newName .= '.' . end($extensions);
                rename($tempName, $newName);
            }

            if (!file_put_contents($newName, $data)) {
                return false;
            }
        } else {
            return false;
        }

        return new UploadedFile([
            'name' => $name,
            'type' => $type,
            'tempName' => $newName
        ]);
    }

    public function applyRules() {
        $refAttributes = array_keys($this->_refAttribues);
        foreach ($refAttributes as $refAttribute) {
            $this->owner->addRule($refAttribute, $this->attributesDefinition[$refAttribute]['validator'], $this->attributesDefinition[$refAttribute]['rules']);
            $this->owner->addRule($refAttribute, $this->attributesDefinition[$refAttribute]['validator'], array_replace_recursive(['on' => ActiveRecord::SCENARIO_UPLOAD], $this->attributesDefinition[$refAttribute]['rules']));
        }
    }

    public function getEntityClass() {
        return empty($this->config['entity_class']) ? $this->owner->className() : $this->config['entity_class'];
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
                    if (!$file->saveAs($userTempDir . $file->name, false)) {
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

    public function detachFile($refAttribute) {
        $this->deleteByRefAttribute($refAttribute);
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
        $idAttribute = $this->entityIdAttribute;
        if (empty($owner->$idAttribute)) {
            throw new Exception('Parent model must have ID when you attaching a file');
        }

        if (!\file_exists($filePath)) {
            throw new Exception("File {$filePath} not exists");
        }

        $fileHash = md5(microtime(true) . $filePath);
        $fileType = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $newFileName = "{$fileHash}.{$fileType}";
        $newFilePath = $this->_organizer->getFullUploadStoreDir($newFileName, $owner, 1, $this->entityIdAttribute) . $newFileName;

        if (!copy($filePath, $newFilePath)) {
            throw new Exception("Cannot copy file! {$filePath}  to {$newFilePath}");
        }

        $file = new $this->attributesDefinition[$attribute]['class'];
        if (isset($this->attributesDefinition[$attribute]['enableVersions'])) {
            $file->setEnableVersions($this->attributesDefinition[$attribute]['enableVersions']);
        }
        
        $file->loadDefaultValues();
        $file->setAttributes([
            'entity_id' => $owner->$idAttribute,
            'entity_class' => $this->getEntityClass(),
            'entity_attribute' => $attribute,
            'hash' => $fileHash,
            'size' => filesize($newFilePath),
            'name' => $extras['name'] ?? $newFileName,
            'label' => $extras['label'] ?? "",
            'content' => $extras['content'] ?? "",
            'extension' => $fileType,
            'mime_type' => FileHelper::getMimeType($newFilePath),
            'logic_path' => $this->_organizer->getUploadLogicPath($fileHash, $owner, 1, $this->entityIdAttribute),
        ]);

        if ($file->save()) {
            if ($this->config['delSrcFileAfterAttach']) {
                @\unlink($filePath);
            }
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
        $idAttribute = $this->entityIdAttribute;
        $fileQuery = $modelClass::find()
                ->andWhere([
            'entity_id' => $this->owner->$idAttribute,
            'entity_class' => $this->getEntityClass(),
            'entity_attribute' => $attribute,
        ]);
        $fileQuery->orderBy($order);

        return $fileQuery->all();
    }

    public function getOneAttachment($attribute, $order = ['position' => SORT_DESC]) {
        $modelClass = $this->attributesDefinition[$attribute]['class'];
        $idAttribute = $this->entityIdAttribute;
        $fileQuery = $modelClass::find()
                ->andWhere([
            'entity_id' => $this->owner->$idAttribute,
            'entity_class' => $this->getEntityClass(),
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
