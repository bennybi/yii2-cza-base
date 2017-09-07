<?php

namespace cza\base\vendor\widgets\plupload;

use Yii;
use yii\web\AssetBundle;

/**
 * Asset bundle for the Plupload script files.
 */
class PluploadAsset extends AssetBundle {

    public $sourcePath = '@cza/base/vendor/widgets/plupload/assets';
    public $js = [
        'plupload.full.min.js',
//        'moxie.min.js',
        'jquery.ui.plupload/jquery.ui.plupload.min.js',
    ];
    public $css = [
        'jquery.ui.plupload/css/jquery.ui.plupload.css',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\jui\JuiAsset',
    ];

    public function init() {
        $this->js[] = 'i18n/' . str_replace("-", "_", Yii::$app->language) . '.js';
        parent::init();
    }

}
