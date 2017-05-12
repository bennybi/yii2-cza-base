<?php

/**
 * refer to arogachev\tree\assets\TreeAsset
 */

namespace cza\base\vendor\assets\jstree;

use yii\web\AssetBundle;

class TreeAsset extends AssetBundle {

    /**
     * @inheritdoc
     */
    public $sourcePath = '@cza/base/vendor/assets/jstree/src';

    /**
     * @inheritdoc
     */
    public $publishOptions = [
        'forceCopy' => YII_DEBUG,
    ];

    /**
     * @inheritdoc
     */
    public $js = [
        'js/tree.js',
    ];
    public $css = [
        'css/themes/proton/style.css',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\YiiAsset',
        'cza\base\vendor\assets\jstree\JsTreeAsset',
    ];

}
