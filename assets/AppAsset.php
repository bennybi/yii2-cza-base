<?php

/**
 * 
 * 
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */

namespace cza\base\assets;

use yii\web\AssetBundle;

/**
 * @author Ben Bi <jianbinbi@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle {

    public $sourcePath = '@cza/base/assets/src';
    public $css = [
        //  https://github.com/select2/select2
//        'plugins/select2/css/select2.min.css',
        // cza
        'css/cza.css',
    ];
    public $js = [
        // https://github.com/select2/select2
//        'plugins/select2/js/select2.full.min.js',
        // cza
//        'js/cza.app.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\widgets\ActiveFormAsset',
        'yii\validators\ValidationAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'cza\base\vendor\assets\MsgGrowl\MsgGrowlAsset',
    ];

}
