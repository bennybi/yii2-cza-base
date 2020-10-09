<?php

/**
 * 
 * 
 * @author Ben Bi <bennybi@qq.com>
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
    public $publishOptions = [
        'forceCopy' => YII_DEBUG,
    ];
    public $css = [
        'css/cza.css', // cza
    ];
    public $js = [
        'js/cza.app.js', // cza
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\widgets\ActiveFormAsset',
        'yii\validators\ValidationAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'ayrozjlc\blockui\BlockUiAsset',
        'cza\base\vendor\assets\MsgGrowl\MsgGrowlAsset',
    ];

}
