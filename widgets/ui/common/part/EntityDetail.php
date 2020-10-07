<?php

namespace cza\base\widgets\ui\common\part;

use Yii;
use cza\base\widgets\Widget;

/**
 * Entity Detail Widget
 * 
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
abstract class EntityDetail extends Widget {

    protected $_tabs = [];
    public $model;
    public $tabTitle = '';
    public $withBaseInfoTab = true;  // need entity model support
    public $withProfileTab = false;  // need (x)Profile model support
    public $withConfigTab = false;  // need (x)Config model support
    public $withTranslationTabs = true; // need (x)Lang model support

    public function getTabsId() {
        if ($this->model) {
            return $this->model->formName() . '-detail-tabs';
        }
        return $this->className() . '-tabs';
    }

    public function getAvailableLangs() {
        if (isset(Yii::$app->params['config']['languages'])) {
            return Yii::$app->params['config']['languages'];
        }
        return [];
    }

    public function getTabItems() {
        $items = [];

        if ($this->withTranslationTabs) {
            $items[] = $this->getTranslationTabItems();
        }

        if ($this->withProfileTab) {
            $items[] = $this->getProfileTab();
        }

        if ($this->withConfigTab) {
            $items[] = $this->getConfigTab();
        }

        if ($this->withBaseInfoTab) {
            $items[] = [
                'label' => Yii::t('app.c2', 'Base Information'),
                'content' => $this->controller->renderPartial('_form', [ 'model' => $this->model,]),
                'active' => true,
            ];
        }

        $items[] = [
            'label' => '<i class="fa fa-th"></i> ' . $this->tabTitle,
            'onlyLabel' => true,
            'headerOptions' => [
                'class' => 'pull-left header',
            ],
        ];

        return $items;
    }

    public function getProfileTab() {
        if (!isset($this->_tabs['PROFILE_TAB'])) {
            if (!$this->model->isNewRecord) {
                $this->_tabs['PROFILE_TAB'] = [
                    'label' => Yii::t('app.c2', 'Profile'),
                    'content' => $this->controller->renderPartial('_profile_form', ['model' => $this->model->profile, 'entityModel' => $this->model]),
                    'enable' => true,
                ];
            } else {
                $this->_tabs['PROFILE_TAB'] = [
                    'label' => Yii::t('app.c2', 'Profile'),
                    'content' => "",
                    'enable' => false,
                ];
            }
        }

        return $this->_tabs['PROFILE_TAB'];
    }

    public function getConfigTab() {
        if (!isset($this->_tabs['CONFIG_TAB'])) {
            if (!$this->model->isNewRecord) {
                $this->_tabs['CONFIG_TAB'] = [
                    'label' => Yii::t('app.c2', 'Config'),
                    'content' => $this->controller->renderPartial('_config_form', ['model' => $this->model->config, 'entityModel' => $this->model]),
                    'enable' => true,
                ];
            } else {
                $this->_tabs['CONFIG_TAB'] = [
                    'label' => Yii::t('app.c2', 'Config'),
                    'content' => "",
                    'enable' => false,
                ];
            }
        }

        return $this->_tabs['CONFIG_TAB'];
    }

    /**
     * 
     * @param type $enable - enable this tab
     * @param type $options - tab options
     * @param type $format
     * @return type
     */
    public function getTranslationTabItems($options = []) {
        if (!isset($this->_tabs['TRANSLATION_TAB'])) {
            $availLangs = $this->getAvailableLangs();
            $items = [];

            foreach ($availLangs as $availLang) {
                $langModel = $this->model->getTranslationModel($availLang);
                $items[] = [
                    'label' => \Yii::t('cza/languages', $availLang),
                    'content' => $this->model->isNewRecord ? "" : $this->controller->renderPartial('_translation_form', ['model' => $langModel, 'entityModel' => $this->model]),
                    'options' => ['id' => $availLang],
                ];
            }

            $this->_tabs['TRANSLATION_TAB'] = [
                'label' => Yii::t('app.c2', 'Multiple Language'),
                'items' => $items,
                'options' => $options,
                'enable' => !$this->model->isNewRecord,
            ];
        }

        return $this->_tabs['TRANSLATION_TAB'];
    }

}
