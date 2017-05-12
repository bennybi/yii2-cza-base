<?php

/**
 * 
 * handle application settings
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */

namespace cza\base\behaviors;

use Yii;
use yii\base\Behavior;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use yii\base\Exception;
use yii\imagine\Image;
use cza\base\models\ActiveRecord;
use cza\base\models\statics\ImageSize;

class ImageBehavior extends Behavior {

    public $versions = [];

    public function init() {
        $this->versions = array_replace_recursive([
            ImageSize::ICON => [ 'resize' => ImageSize::getSize(ImageSize::ICON)],
            ImageSize::THUMBNAIL => [ 'resize' => ImageSize::getSize(ImageSize::THUMBNAIL)],
            ImageSize::MEDIUM => [ 'resize' => ImageSize::getSize(ImageSize::MEDIUM)],
                ], $this->versions);
    }

    public function events() {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'handle',
            ActiveRecord::EVENT_AFTER_UPDATE => 'handle',
        ];
    }

    public function handle($event) {
        $owner = $this->owner;
        $oriImagePath = $owner->getStorePath();
        $oriImageName = basename($oriImagePath);
        $oriImageDir = $owner->getStoreDir();


        if (\file_exists($oriImagePath)) {
            $image = Image::getImagine();
            foreach ($this->versions as $version => $actions) {
                foreach ($actions as $method => $args) {
                    array_unshift($args, $oriImagePath);
                    $image = call_user_func_array("yii\imagine\Image::thumbnail", $args);
//                    $image = call_user_func_array("yii\imagine\Image::{$method}", $args);
                }
                $resizePath = $this->getResizePath($oriImageDir, $version) . '/' . $oriImageName;
                $image->save($resizePath);
            }
        } else {
            throw new Exception(Yii::t('cza', 'File {s1} not existed!', ['s1' => $oriImagePath]));
        }
    }

    public function getResizePath($oriImageDir, $version) {
        $path = $oriImageDir . '/' . $version;
        if (!\file_exists($path)) {
            FileHelper::createDirectory($path);
        }
        return $path;
    }

}
