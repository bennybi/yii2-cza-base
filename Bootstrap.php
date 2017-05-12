<?php

/**
 * ActiveForm is a widget that builds an interactive HTML form for one or multiple data models.
 * 
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */

namespace cza\base;

use Yii;
use yii\base\BootstrapInterface;

/**
 * This constant defines the framework installation directory.
 */
defined('CZA_PATH') or define('CZA_PATH', __DIR__);

class Bootstrap extends \yii\base\Component implements BootstrapInterface {

    /**
     * - route translations
     * @param \yii\base\Application $app
     */
    public function bootstrap($app) {
//        \Yii::info(__CLASS__);
//        $app->attachBehavior('AppConfigBehavior', 'cza\base\behaviors\AppConfigBehavior');
//        
        $app->i18n->translations['cza*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@cza/base/messages',
            'fileMap' => [
                'cza' => 'default.php',
                'cza/languages' => 'languages.php',
            ],
        ];
        
        // declare CZA Helper
        $app->set('czaHelper', [
            'class' => '\cza\base\helpers\Helper',
        ]);
    }

}
