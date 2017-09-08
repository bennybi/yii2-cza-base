<?php

namespace cza\base\widgets\Album;

use Yii;
use yii\web\AssetBundle;

/**
 * Asset bundle for the Album script files.
 */
class AlbumAsset extends AssetBundle {

    public $sourcePath = '@cza/base/widgets/Album/assets';
    public $js = [
        'jquery.fancybox.min.js',
    ];
    public $css = [
        'jquery.fancybox.min.css',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
