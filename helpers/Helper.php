<?php

/**
 * 
 * 
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */

namespace cza\base\helpers;

use Yii;
use yii\helpers\Url;

/**
 *  Act as CCA software helper
 * 
 *  @property \cza\base\components\utils\FolderOrganizer $folderOrganizer
 *  @property \cza\base\vendor\utils\SimpleHTMLDOM\SimpleHTMLDOM $simpleHTMLDOM
 *  @author Ben Bi <ben@cciza.com>
 *  @since 2.0
 * 
 * 
 * @property yii\web\AssetBundle AppAsset
 * 
 */
class CzaHelper extends \yii\di\ServiceLocator {

    /**
     * @var array for data caching
     * ini - external ini variables
     * env - running environment variables
     */
    private $_data = [
        'ini' => [],
        'env' => [],
    ];

    public function init() {
        $this->registerCoreComponents();
    }

    /**
     * register core components can be called in apps
     * folderOrganizer - create/caching cza app's folders
     * simpleHTMLDOM - parse html tags
     */
    public function registerCoreComponents() {
        foreach ($this->coreComponents() as $id => $config) {
            $this->set($id, $config);
        }
    }

    /**
     * Returns the configuration of core application components.
     * @see set()
     */
    public function coreComponents() {
        return [
            'folderOrganizer' => [
                'class' => '\cza\base\components\utils\FolderOrganizer',
                'uploadFolderName' => isset(Yii::$app->params['config']['upload']['path']) ? Yii::$app->params['config']['upload']['path'] : 'uploads',
            ],
            'simpleHTMLDOM' => [
                'class' => '\cza\base\vendor\utils\SimpleHTMLDOM\SimpleHTMLDOM',
            ],
        ];
    }

    /**
     * @return current version
     */
    public function version() {
        if (!isset($this->_data['ini']['version'])) {
            $this->getIni();
        }
        return $this->_data['ini']['version'];
    }

    public function getIni() {
        if (!isset($this->_data['ini'])) {
            $this->_data['ini'] = parse_ini_file(dirname(__DIR__) . "/settings.ini");
        }
        return $this->_data['ini'];
    }

    /**
     * assets for backend 
     */
    public function registerBackendAssets($view = null) {
        if (is_null($view)) {
            $view = \Yii::$app->getView();
        }
        \cza\base\assets\AppAsset::register($view);
        \cza\base\vendor\assets\MsgGrowl\MsgGrowlAsset::register($view);
    }

    /**
     * set current application asset url
     */
    public function setBackendAssetUrl($url) {
        $this->_data['env']['BACKEND_ASSETS_URL'] = $url;
    }

    /**
     * get current application asset url
     * @return string
     */
    public function getBackendAssetUrl($asset = '') {
        if (isset($this->_data['env']['BACKEND_ASSETS_URL'])) {
            return $this->_data['env']['BACKEND_ASSETS_URL'];
        }

        $bundle = Yii::$app->getAssetManager()->getBundle('backend\themes\\' . CCA2_BACKEND_THEME . '\components\AppAsset');
        $this->_data['env']['BACKEND_ASSETS_URL'] = Yii::$app->getAssetManager()->getAssetUrl($bundle, $asset);
        return $this->_data['env']['BACKEND_ASSETS_URL'];
    }

    /**
     * 
     * @param type $caching
     * @return array('lang code' => 'lang label)
     */
    public function getEnabledLangs($caching = true) {
        if (isset($this->_data['env']['ENABLED_LANGS'])) {
            return $this->_data['env']['ENABLED_LANGS'];
        }

        if ($caching) {
            $this->cachingEnvVariables('ENABLED_LANGS', function() {
                $data = [];
                foreach (Yii::$app->params['config']['languages'] as $lang) {
                    $data[$lang] = Yii::t('cza/languages', $lang);
                }
                return $data;
            });
        }
        return $this->_data['env']['ENABLED_LANGS'];
    }

    /**
     *
     * @param type $size - 64, 54, 48, 32
     * @param type $absolute
     * @return image url 
     */
    public function getCcizaLogoUrl($size = 64, $absolute = false) {
        if (isset($this->_data['env']["logo_{$size}x{$size}"])) {
            return $this->_data['env']["logo_{$size}x{$size}"];
        }

        $logo = "logo_{$size}x{$size}.png";
        $this->_data['env']["logo_{$size}x{$size}"] = Url::to("@web/images/cciza_logo/{$logo}");
//        $this->_data['env']["logo_{$size}x{$size}"] = Url::home() . "images/cciza_logo/{$logo}";
        return $this->_data['env']["logo_{$size}x{$size}"];
    }

    /**
     * 
     * @param type $key
     * @param mix $value - it could be scalar value or callback
     * @param type $duration
     */
    public function cachingEnvVariables($key, $value, $duration = 0) {
        $this->_data['env'][$key] = Yii::$app->cache->get("ENV_{$key}");
        if ($this->_data['env'][$key] === false) {
            if (is_callable($value)) {
                $this->_data['env'][$key] = call_user_func($value);
            } else {
                $this->_data['env'][$key] = $value;
            }
            Yii::$app->cache->set("ENV_{$key}", $this->_data['env'][$key], $duration);
        }
        return $this->_data['env'][$key];
    }

}
