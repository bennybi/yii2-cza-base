<?php

/**
 * 
 * handle medias(image/files) attached in cms content fields
 * @author Ben Bi <bennybi@qq.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */

namespace cza\base\behaviors;

use Yii;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use cza\base\interfaces\ITranslationActiveRecord;

/**
 * Descriptions
 * attache media-operations of cms fields when (after create/before update/before delete) the model
 *
 * @author Ben Bi <bennybi@qq.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
class CmsMediaBehavior extends \cza\base\behaviors\AttributeBehavior {

    protected $_htmlParser = null;
    protected $_isSetup = false;
    public $fields = [];

    /*
     * defines config params on running, it is array, default is
     * array(
      'srcBasePath' => '',
      'dstBasePath' => '',
      'path' => 'uploads',
      'underOwnerEntityPath' => true,  // if set to true, will look for owner entity's path
      'createMode' => 0775,
      'cachingPath' => '',
      );
     */
    public $config = array();

    /*
     * defines switch options on running, , it is array, default is
      array(
      'isTranslation' => false, // save translation fields' medias, also store into src model caching folder
      'handleFiles' => true,
      'handleImages' => true,
      );
     */
    public $options = array();

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();

        $this->setup();

        if (!empty($this->fields)) {
            foreach ($this->fields as $field) {
                $this->attributes[BaseActiveRecord::EVENT_AFTER_INSERT][] = $field;
                $this->attributes[BaseActiveRecord::EVENT_BEFORE_UPDATE][] = $field;
            }
        }
    }

    public function events() {
        $events = parent::events();
        $events[BaseActiveRecord::EVENT_BEFORE_DELETE] = 'beforeDelete';
        return $events;
    }

    /**
     * setup config on flying
     */
    protected function setup() {
        if (!$this->_isSetup) {
            $defaultConfig = array(
                'srcBasePath' => Yii::getAlias('@webroot'),
                'dstBasePath' => Yii::$app->czaHelper->folderOrganizer->getEntityUploadPath(true),
                'dstUrlBase' => Yii::$app->czaHelper->folderOrganizer->getEntityUploadPath(),
                'underOwnerEntityPath' => true,
                'createMode' => 0775,
                'cachingPath' => '',
            );
            $this->config = \yii\helpers\ArrayHelper::merge($defaultConfig, $this->config);


            $defaultOptions = array(
                'isTranslation' => false,
                'handleFiles' => true,
                'handleImages' => true,
            );
            $this->options = \yii\helpers\ArrayHelper::merge($defaultOptions, $this->options);
            $this->_isSetup = true;
        }
    }

    public function getHtmlParser() {
        if (is_null($this->_htmlParser))
            $this->_htmlParser = Yii::$app->czaHelper->simpleHTMLDOM;
        return $this->_htmlParser;
    }

    /**
     * @inheritdoc
     */
    protected function getValue($event, $attribute) {
        if (!$this->options['isTranslation']) {
            $this->value = $this->_handleFieldContent($attribute, $this->owner->$attribute);
        } else {
            if ($this->owner instanceof ITranslationActiveRecord) {
                $this->value = $this->_handleTransFieldContent($attribute, $this->owner->$attribute);
            } else {
                throw new \ErrorException(\Yii::t('cza', 'Owner model must instanceof ITranslationActiveRecord!'));
            }
        }

        if ($event->name == BaseActiveRecord::EVENT_AFTER_INSERT) {
            $this->owner->updateAttributes(array_fill_keys((array) $attribute, $this->value));
        }
        return $this->value;
    }

    protected function _handleFieldContent($field, $v) {
        if (!empty($v)) {
            $html = $this->getHtmlParser()->str_get_html($v);
            if ($this->options['handleImages']) {
                $v = $this->_handleImages($html, $field, $v);
            }
            if ($this->options['handleFiles']) {
                $v = $this->_handleFiles($html, $field, $v);
            }
            return $v;
        }
        return '';
    }

    protected function _handleImages(&$html, $field, $v) {
        $tmpMap = array();
        $newContent = '';
        $folderOrganizer = Yii::$app->czaHelper->folderOrganizer;
        foreach ($html->find('img') as $element) {
            $filename = basename($element->src);
            $srcFilePath = $this->config['srcBasePath'] . DIRECTORY_SEPARATOR . $element->src;
            if (@file_exists($srcFilePath)) {
                if ($this->config['underOwnerEntityPath'])
                    $cachingPath = $folderOrganizer->setupCachingPath($this->config['dstBasePath'], $filename, $this->owner);
                else
                    $cachingPath = $folderOrganizer->setupCachingPath($this->config['dstBasePath'], $filename);

                $dstFilePath = $this->config['dstBasePath'] . DIRECTORY_SEPARATOR . $cachingPath . DIRECTORY_SEPARATOR . $filename;
                $dstFileUrl = $this->config['dstUrlBase'] . DIRECTORY_SEPARATOR . $cachingPath . DIRECTORY_SEPARATOR . $filename;

                if (@file_exists($dstFilePath)) {  // be used to correct repeate post content when stay on same dialog
                    $tmpMap[$element->src] = str_replace('\\', '/', $dstFileUrl);
                    continue;
                }

                if (($srcFilePath != $dstFilePath)) {
                    $tmpMap[$element->src] = str_replace('\\', '/', $dstFileUrl);
                    @rename($srcFilePath, $dstFilePath);
                }
            }
        }
        $newContent = strtr($v, $tmpMap);
        return $newContent;
    }

    protected function _handleFiles(&$html, $field, $v) {
        $tmpMap = array();
        $newContent = '';
        $folderOrganizer = Yii::$app->czaHelper->folderOrganizer;
        foreach ($html->find('a') as $element) {
            if (!filter_var($element->href, FILTER_VALIDATE_URL)) {
                $filename = basename($element->href);
                $srcFilePath = $this->config['srcBasePath'] . DIRECTORY_SEPARATOR . $element->href;
                if (@file_exists($srcFilePath)) {
                    if ($this->config['underOwnerEntityPath'])
                        $cachingPath = $folderOrganizer->setupCachingPath($this->config['dstBasePath'], $filename, $this->owner);
                    else
                        $cachingPath = $folderOrganizer->setupCachingPath($this->config['dstBasePath'], $filename);

                    $dstFilePath = $this->config['dstBasePath'] . DIRECTORY_SEPARATOR . $cachingPath . DIRECTORY_SEPARATOR . $filename;
                    $dstFileUrl = $this->config['dstUrlBase'] . DIRECTORY_SEPARATOR . $cachingPath . DIRECTORY_SEPARATOR . $filename;

                    if (@file_exists($dstFilePath)) {  // be used to correct repeate post content when stay on same dialog
                        $tmpMap[$element->href] = str_replace('\\', '/', $dstFileUrl);
                        continue;
                    }

                    if (($srcFilePath != $dstFilePath)) {
                        $tmpMap[$element->href] = str_replace('\\', '/', $dstFileUrl);
                        @rename($srcFilePath, $dstFilePath);
                    }
                }
            }
        }
        $newContent = strtr($v, $tmpMap);
        return $newContent;
    }

    protected function _handleTransFieldContent($field, $v) {
        if (!empty($v)) {
            $html = $this->getHtmlParser()->str_get_html($v);
            if ($this->options['handleImages']) {
                $v = $this->_handleTransImages($html, $field, $v);
            }
            if ($this->options['handleFiles']) {
                $v = $this->_handleTransFiles($html, $field, $v);
            }
            return $v;
        }
        return '';
    }

    protected function _handleTransImages(&$html, $field, $v) {
        $tmpMap = array();
        $newContent = '';
        $folderOrganizer = Yii::$app->czaHelper->folderOrganizer;
        foreach ($html->find('img') as $element) {
            $filename = basename($element->src);
            $srcFilePath = $this->config['srcBasePath'] . DIRECTORY_SEPARATOR . $element->src;
            if (@file_exists($srcFilePath)) {
                if ($this->config['underOwnerEntityPath'])
                    $cachingPath = $folderOrganizer->setupCachingPath($this->config['dstBasePath'], $filename, $this->owner->srcModel);
                else
                    $cachingPath = $folderOrganizer->setupCachingPath($this->config['dstBasePath'], $filename);

                $dstFilePath = $this->config['dstBasePath'] . DIRECTORY_SEPARATOR . $cachingPath . DIRECTORY_SEPARATOR . $filename;
                $dstFileUrl = $this->config['dstUrlBase'] . DIRECTORY_SEPARATOR . $cachingPath . DIRECTORY_SEPARATOR . $filename;

                if (@file_exists($dstFilePath)) {  // be used to correct repeate post content when stay on same dialog
                    $tmpMap[$element->src] = str_replace('\\', '/', $dstFileUrl);
                    continue;
                }

                if (($srcFilePath != $dstFilePath)) {
                    $tmpMap[$element->src] = str_replace('\\', '/', $dstFileUrl);
                    @rename($srcFilePath, $dstFilePath);
                }
            }
        }
        $newContent = strtr($v, $tmpMap);
        return $newContent;
    }

    protected function _handleTransFiles(&$html, $field, $v) {
        $tmpMap = array();
        $newContent = '';
        $folderOrganizer = Yii::$app->czaHelper->folderOrganizer;
        foreach ($html->find('a') as $element) {
            if (!filter_var($element->href, FILTER_VALIDATE_URL)) {
                $filename = basename($element->href);
                $srcFilePath = $this->config['srcBasePath'] . DIRECTORY_SEPARATOR . $element->href;
                if (@file_exists($srcFilePath)) {
                    if ($this->config['underOwnerEntityPath'])
                        $cachingPath = $folderOrganizer->setupCachingPath($this->config['dstBasePath'], $filename, $this->owner->srcModel);
                    else
                        $cachingPath = $folderOrganizer->setupCachingPath($this->config['dstBasePath'], $filename);

                    $dstFilePath = $this->config['dstBasePath'] . DIRECTORY_SEPARATOR . $cachingPath . DIRECTORY_SEPARATOR . $filename;
                    $dstFileUrl = $this->config['dstUrlBase'] . DIRECTORY_SEPARATOR . $cachingPath . DIRECTORY_SEPARATOR . $filename;

                    if (@file_exists($dstFilePath)) {  // be used to correct repeate post content when stay on same dialog
                        $tmpMap[$element->href] = str_replace('\\', '/', $dstFileUrl);
                        continue;
                    }

                    if (($srcFilePath != $dstFilePath)) {
                        $tmpMap[$element->href] = str_replace('\\', '/', $dstFileUrl);
                        @rename($srcFilePath, $dstFilePath);
                    }
                }
            }
        }
        $newContent = strtr($v, $tmpMap);
        return $newContent;
    }

    public function beforeDelete($event) {
        try {
            if (!$this->options['isTranslation']) {
                $model = $this->owner;
                $dstFilePath = $this->config['dstBasePath'] . DIRECTORY_SEPARATOR . basename($model->className()) . DIRECTORY_SEPARATOR . $model->id;

                if (@file_exists($dstFilePath)) {
                    \yii\helpers\FileHelper::removeDirectory($dstFilePath);
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

}
