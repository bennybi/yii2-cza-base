<?php

namespace cza\base\widgets\ui\common\box;

use Yii;
use yii\base\InvalidCallException;
use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/**
 * Description of LanguageSelectBox
 *
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
class LanguageSelectBox extends Widget {

    public $name = "language";
    public $actionUrl = "env-settings";
    public $returnUrl = "";

    public function run() {
        echo Html::beginForm(Url::to([$this->actionUrl]), 'post', []);
        if (!empty($this->returnUrl)) {
            echo Html::hiddenInput('returnUrl', $this->returnUrl);
        }
        echo Html::dropDownList('language', Yii::$app->language, Yii::$app->czaHelper->getEnabledLangs(), ['id' => $this->htmlId(), 'onchange' => 'this.form.submit()']);
        echo Html::endForm();
    }

    /**
     * @return array dropdown menu items
     */
    public static function getDropdownItems($params = []) {
        $default = [
            'actionUrl' => 'env-settings',
            'checkedIcon' => 'fa fa-check',
            'unchekedIcon' => 'fa fa-circle-thin',
        ];
        $params = ArrayHelper::merge($default, $params);
        $items = [];
        $enabledLangs = Yii::$app->czaHelper->getEnabledLangs();
        foreach ($enabledLangs as $code => $label) {
            if ($code == Yii::$app->language) {
                $enabledIcon = $params['checkedIcon'];
            } else {
                $enabledIcon = $params['unchekedIcon'];
            }
            $items[] = ['label' => $label, 'url' => Url::to([$params['actionUrl'], 'language' => $code]), 'visible' => true, 'icon' => $enabledIcon];
        }

        return $items;
    }

}
