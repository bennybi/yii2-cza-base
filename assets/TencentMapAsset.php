<?php

/**
 * 
 * 
 * @author Ben Bi <bennybi@qq.com>
 * @license
 */

namespace cza\base\assets;

use yii\web\AssetBundle;

/**
 * @author Ben Bi <jianbinbi@gmail.com>
 * @since 2.0
 */
class TencentMapAsset extends AssetBundle {

//    public $sourcePath = '@webroot/themes/' . CZA_FRONTEND_THEME;
//    public $publishOptions = [
//        'forceCopy' => YII_DEBUG,
//    ];
//    public $css = [];
    public $js = [
//        'http://map.qq.com/api/js?v=2.exp',
        'http://map.qq.com/api/js?v=2.exp&libraries=convertor',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
