<?php

namespace cza\base\vendor\widgets\plupload;

use Yii;
use cza\base\widgets\Widget;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * Wrapper for Plupload
 * A multiple file upload utility using Flash, Silverlight, Google Gears, HTML5 or BrowserPlus.
 * @url http://www.plupload.com/
 * @version 1.0
 * @author Bound State Software
 */
class Plupload extends Widget {

    /**
     * Page URL or action to where the files will be uploaded to.
     * @var mixed
     */
    public $url;
    public $htmlOptions = [];

    /**
     * ID of the error container.
     * @var string
     */
    public $errorContainer;

    /**
     * Options to pass directly to the JavaScript plugin.
     * Please refer to the Plupload documentation:
     * @link http://www.plupload.com/documentation.php
     * @var array
     */
    public $options = [];

    /**
     * The JavaScript event callbacks to attach to Plupload object.
     * @link http://www.plupload.com/example_events.php
     * In addition to the standard events, this widget adds a "FileSuccess"
     * event that is fired when a file is uploaded without error.
     * NOTE: events signatures should all have a first argument for event, in 
     * addition to the arguments documented on the Plupload website.
     * @var array
     */
    public $events = [];

    /**
     * @return int the max upload size in MB
     */
    public static function getPHPMaxUploadSize() {
        $max_upload = (int) (ini_get('upload_max_filesize'));
        $max_post = (int) (ini_get('post_max_size'));
        $memory_limit = (int) (ini_get('memory_limit'));
        return min($max_upload, $max_post, $memory_limit);
    }

    /**
     * @inheritdoc
     */
    public function init() {
        // Make sure URL is provided
        if (empty($this->url))
            throw new Exception(Yii::t('yii', '{class} must specify "url" property value.', array('{class}' => get_class($this))));

        if (!isset($this->htmlOptions['id']))
            $this->htmlOptions['id'] = $this->getId();

        if (!isset($this->options['multipart_params']))
            $this->options['multipart_params'] = [];

        $this->options['multipart_params'][Yii::$app->request->csrfParam] = Yii::$app->request->csrfToken;

        $bundle = PluploadAsset::register($this->view);
        $defaultOptions = [
            'runtimes' => 'html5,flash,silverlight,html4',
            'url' => Url::to($this->url),
            'max_file_count' => 20,
//            'chunk_size' => '1mb',
            'chunk_size' => '500kb',
//            'resize' => [
//                'width' => 200,
//                'height' => 200,
//                'quality' => 90,
//                'crop' => true // crop to exact dimensions
//            ],
            'filters' => [
                'max_file_size' => '1000mb',
                'mime_types' => [
                    ['title' => "Image files", 'extensions' => "jpg,gif,png"],
//                    ['title' => "Zip files", 'extensions' => "zip"]
                ]
            ],
            'rename' => true,
            'sortable' => true,
            'dragdrop' => true,
            'views' => [
                'list' => true,
                'thumbs' => true,
                'active' => 'thumbs',
            ],
            'flash_swf_url' => "{$bundle->baseUrl}/Moxie.swf",
            'silverlight_xap_url' => "{$bundle->baseUrl}/Moxie.xap",
        ];

        $this->options = ArrayHelper::merge($defaultOptions, $this->options);
    }

    public function run() {
        // Output
        echo Html::beginTag('div', $this->htmlOptions);
        echo Html::endTag('div');

        // Generate event JavaScript
        $events = '';
        foreach ($this->events as $event => $callback) {
            $events .= "uploader.bind('$event', $callback);\n";
        }
//        $this->view->registerJs("var uploader = new plupload.Uploader(" . Json::encode($this->options) . ");\nuploader.init();\n{$events}");
        $js = "";
        $js .= "$('#{$this->htmlOptions['id']}').plupload(" . Json::encode($this->options) . ");\n";
        $js .= "var uploader = jQuery('#{$this->htmlOptions['id']}').plupload('getUploader');\n";
        $js .= "{$events};\n";
        $this->view->registerJs($js);
    }

}
