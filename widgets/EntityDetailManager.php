<?php

namespace cza\base\widgets;

use Yii;
use yii\base\InvalidCallException;
use yii\base\Model;
use yii\helpers\Html;

/**
 * Entity Detail Widget
 * 
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
class EntityDetailManager extends Widget {

    protected $_tabs = [];
    public $model;

    public function getTabId() {
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

    /**
     * 
     * @param type $enable - enable this tab
     * @param type $options - tab options
     * @param type $format
     * @return type
     */
    public function getTranslationTabs($enable = true, $options = [], $format = Widget::UI_BOOTSTRAP) {
        if (!isset($this->_tabs['TRANSLATION_TAB'])) {
            $availLangs = $this->getAvailableLangs();
            $items = [];

            foreach ($availLangs as $availLang) {
                $langModel = $this->model->getTranslation($availLang);
                $items[] = [
                    'label' => \Yii::t('cza/languages', $availLang),
                    'content' => $this->model->isNewRecord ? "" : $this->controller->renderPartial('_translation_form', ['model' => $langModel, 'ownerModel' => $this->model]),
                    'options' => ['id' => $availLang],
                ];
            }

            $this->_tabs['TRANSLATION_TAB'] = [
                'label' => Yii::t('app', 'Multiple Language'),
                'items' => $items,
                'options' => $options,
                'enable' => $enable,
            ];
        }

        return $this->_tabs['TRANSLATION_TAB'];
    }

}
