<?php

namespace cza\base\models\entity;

use Yii;
use yii\base\Exception;
use yii\helpers\Url;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "{{%entity_file}}".
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
class EntityAttachedFile extends EntityAttachments {

    public function init() {
        parent::init();
        $this->type = self::TYPE_FILE;
    }

    public static function find() {

        return parent::find()->where(['type' => self::TYPE_FILE]);
    }

}
