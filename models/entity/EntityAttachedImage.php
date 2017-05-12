<?php

namespace cza\base\models\entity;

use Yii;
use yii\base\Exception;
use yii\helpers\Url;
use yii\helpers\FileHelper;
use cza\base\models\statics\ImageSize;

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
class EntityAttachedImage extends EntityAttachments {

    public function init() {
        parent::init();
        $this->type = self::TYPE_IMAGE;
    }

    public static function find() {
        return parent::find()->where(['type' => 1]);
    }

    public function behaviors() {
        return array_replace_recursive(parent::behaviors(), [
            'image' => [
                'class' => \cza\base\behaviors\ImageBehavior::className(),
            ]
        ]);
    }

    public function getIcon() {
        if (!isset($this->_data['icon'])) {
            $this->_data['icon'] = ImageSize::ICON . '/' . $this->name;
        }
        return $this->_data['icon'];
    }

    public function getIconUrl() {
        if (!isset($this->_data['iconUrl'])) {
            $this->_data['iconUrl'] = $this->getStoreUrl() . '/' . $this->getLogicPath() . $this->getIcon();
        }
        return $this->_data['iconUrl'];
    }

    public function getThumbnail() {
        if (!isset($this->_data['thumbnail'])) {
            $this->_data['thumbnail'] = ImageSize::THUMBNAIL . '/' . $this->getHashFileName();
        }
        return $this->_data['thumbnail'];
    }

    public function getThumbnailUrl() {
        if (!isset($this->_data['thumbnailUrl'])) {
            $this->_data['thumbnailUrl'] = $this->getStoreUrl() . '/' . $this->getLogicPath() . $this->getThumbnail();
        }
        return $this->_data['thumbnailUrl'];
    }

    public function getOrginal() {
        if (!isset($this->_data['orginal'])) {
            $this->_data['orginal'] = $this->getHashFileName();
        }
        return $this->_data['orginal'];
    }

    public function getOriginalUrl() {
        if (!isset($this->_data['orginalUrl'])) {
            $this->_data['orginalUrl'] = $this->getStoreUrl() . '/' . $this->getLogicPath() . $this->getOrginal();
        }
        return $this->_data['orginalUrl'];
    }

    public function getMedium() {
        if (!isset($this->_data['medium'])) {
            $this->_data['medium'] = ImageSize::MEDIUM . '/' . $this->getHashFileName();
        }
        return $this->_data['medium'];
    }

    public function getMediumUrl() {
        if (!isset($this->_data['mediumUrl'])) {
            $this->_data['mediumUrl'] = $this->getStoreUrl() . '/' . $this->getLogicPath() . $this->getMedium();
        }
        return $this->_data['mediumUrl'];
    }

    public function getUrlByFormat($format = ImageSize::MEDIUM) {
        $url = "";
        switch ($format) {
            case ImageSize::ICON:
                $url = $this->getIconUrl();
                break;
            case ImageSize::MEDIUM:
                $url = $this->getMediumUrl();
                break;
            case ImageSize::ORGINAL:
                $url = $this->getOriginalUrl();
                break;
            case ImageSize::THUMBNAIL:
                $url = $this->getThumbnailUrl();
                break;
            default:
                $url = $this->getMediumUrl();
                break;
        }
        return $url;
    }

}
