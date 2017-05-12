<?php

namespace cza\base\models\entity;

use Yii;
use yii\base\Exception;
use yii\helpers\Url;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "{{%entity_attachments}}".
 *
 * @property integer $id
 * @property integer $entity_id
 * @property string $entity_class
 * @property integer $type
 * @property string $name
 * @property string $label
 * @property string $hash
 * @property string $extension
 * @property integer $size
 * @property string $mime_type
 * @property string $logic_path
 * @property integer $status
 * @property integer $position
 * @property string $created_at
 * @property string $updated_at
 */
class EntityAttachments extends \cza\base\models\ActiveRecord {

    const TYPE_FILE = 0;
    const TYPE_IMAGE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%entity_attachments}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['entity_id', 'type', 'size', 'status', 'position'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['entity_class', 'name', 'label', 'hash', 'extension', 'mime_type', 'logic_path'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app.c2', 'ID'),
            'entity_id' => Yii::t('app.c2', 'Entity ID'),
            'entity_class' => Yii::t('app.c2', 'Entity Class'),
            'type' => Yii::t('app.c2', 'Type'),
            'name' => Yii::t('app.c2', 'Name'),
            'label' => Yii::t('app.c2', 'Label'),
            'hash' => Yii::t('app.c2', 'Hash'),
            'extension' => Yii::t('app.c2', 'Extension'),
            'size' => Yii::t('app.c2', 'Size'),
            'mime_type' => Yii::t('app.c2', 'Mime Type'),
            'logic_path' => Yii::t('app.c2', 'Logic Path'),
            'status' => Yii::t('app.c2', 'Status'),
            'position' => Yii::t('app.c2', 'Position'),
            'created_at' => Yii::t('app.c2', 'Created At'),
            'updated_at' => Yii::t('app.c2', 'Updated At'),
        ];
    }

    public function getDownloadUrl() {
        return Url::to(['/attachments/file/download', 'id' => $this->id]);
    }

    public function getHashFileName() {
        return "{$this->hash}.{$this->extension}";
    }

    public function getFileName() {
        return "{$this->name}.{$this->extension}";
    }

    public function getStoreUrl() {
        if (!isset($this->_data['storeUrl'])) {
            $this->_data['storeUrl'] = Yii::$app->czaHelper->folderOrganizer->getUploadStoreUrl();
        }
        return $this->_data['storeUrl'];
    }

    public function getStorePath() {
        if (!isset($this->_data['storePath'])) {
            $this->_data['storePath'] = Yii::$app->czaHelper->folderOrganizer->getFullUploadStorePath($this);
        }
        return $this->_data['storePath'];
    }

    public function getStoreDir() {
        if (!isset($this->_data['storeDir'])) {
            $this->_data['storeDir'] = dirname($this->getStorePath());
        }
        return $this->_data['storeDir'];
    }

    public function getLogicPath() {
        return $this->logic_path;
    }

    public function beforeDelete() {
        $filePath = $this->getStorePath();
        if (@\file_exists($filePath)) {
            $dir = dirname($filePath);
            FileHelper::removeDirectory($dir);
        } else {
            if (YII_DEBUG) {
                throw new Exception("Can not detect {$filePath}");
            }
        }
        return parent::beforeDelete();
    }

    /**
     * @inheritdoc
     * @return \cza\base\models\query\EntityAttachmentsQuery the active query used by this AR class.
     */
    public static function find() {
        return new \cza\base\models\query\EntityAttachmentsQuery(get_called_class());
    }

}
