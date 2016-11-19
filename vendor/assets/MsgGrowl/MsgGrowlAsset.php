<?php

/**
 * 
 * 
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */

namespace cza\base\vendor\assets\MsgGrowl;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class MsgGrowlAsset extends AssetBundle {

    public $sourcePath = '@cza/base/vendor/assets/MsgGrowl/src';

    public $css = [
        'css/msgGrowl.css',
    ];
    public $js = [
//        'js/msgGrowl.min.js'
        'js/msgGrowl.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
