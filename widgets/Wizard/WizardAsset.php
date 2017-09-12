<?php

namespace cza\base\widgets\Wizard;

use Yii;
use yii\web\AssetBundle;
use cza\base\widgets\Wizard\Wizard;

/**
 * Asset bundle for the Wizard script files.
 */
class WizardAsset extends AssetBundle {

    public $sourcePath = '@cza/base/widgets/Wizard/assets';
    public $js = [
        'js/jquery.smartWizard.min.js',
    ];
    public $css = [
        'css/smart_wizard.min.css',
//        'css/smart_wizard_theme_arrows.min.css',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];

    public function applyTheme($name) {
        if (in_array($name, [Wizard::THEME_ARROWS, Wizard::THEME_CIRCLES, Wizard::THEME_DOTS])) {
            $this->css[] = "css/smart_wizard_theme_{$name}.min.css";
        }
    }

}
