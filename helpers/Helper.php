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
class Helper extends \yii\di\ServiceLocator {

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
            'naming' => [
                'class' => '\cza\base\components\utils\Naming',
            ],
//            'folderOrganizer' => [
//                'class' => '\cza\base\components\utils\FolderOrganizer',
//                'uploadFolderName' => isset(Yii::$app->params['config']['upload']['path']) ? Yii::$app->params['config']['upload']['path'] : 'uploads',
//            ],
//            'simpleHTMLDOM' => [
//                'class' => '\cza\base\vendor\utils\SimpleHTMLDOM\SimpleHTMLDOM',
//            ],
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
    }

    /**
     * set current application asset url
     */
    public function setBackendAssetUrl($url) {
        $key = "BACKEND_ASSETS_URL";
        $value = $url;
        return $this->setEnvData($key, $value);
    }

    /**
     * get current application asset url
     * @return string
     */
//    public function getBackendAssetUrl($asset = '') {
//        if (isset($this->_data['env']['BACKEND_ASSETS_URL'])) {
//            return $this->_data['env']['BACKEND_ASSETS_URL'];
//        }
//
//        $bundle = Yii::$app->getAssetManager()->getBundle('backend\themes\\' . CCA2_BACKEND_THEME . '\components\AppAsset');
//        $key = "BACKEND_ASSETS_URL";
//        $value = Yii::$app->getAssetManager()->getAssetUrl($bundle, $asset);
//        return $this->setEnvData($key, $value);
//    }

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
     * @param string $key
     * @return null or sth else
     */
    public function setEnvData($key, $value, $caching = true, $duration = 0) {
        if ($caching) {
            return $this->cachingEnvVariables($key, $value, $duration);
        } else {
            if ($this->_data['env'][$key] === false) {
                $this->_data['env'][$key] = is_callable($value) ? call_user_func($value) : $value;
            }
            return $this->_data['env'][$key];
        }
    }

    /**
     * 
     * @param string $key
     * @return null or sth else
     */
    public function getEnvData($key) {
        if (isset($this->_data['env'][$key])) {
            return $this->_data['env'][$key];
        }
        return null;
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

        $key = "logo_{$size}x{$size}.png";
        $value = Url::to("@web/images/cciza_logo/{$logo}");
        return $this->setEnvData($key, $value);
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
            $this->_data['env'][$key] = is_callable($value) ? call_user_func($value) : $value;
            Yii::$app->cache->set("ENV_{$key}", $this->_data['env'][$key], $duration);
        }
        return $this->_data['env'][$key];
    }

}
