<?php

namespace cza\base\widgets\navigation;

use Yii;
use yii\base\InvalidCallException;
use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use cza\base\widgets\Widget;

/**
 * Top Naviation Bar Widget
 * depends on bootstrap & jquery lib, for bootstrap 3.0
 * 
 * it is composed by three parts:
 *            brand / search / menu
 * 
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
class TopNavBar extends Widget {

    public $enables = [];
    public $brandOptions = [];
    public $menuOptions = [];
    
    public function init() {
        parent::init();
        $defaults = array('enableBrand' => true, 'enableMenu' => true);
        $this->enables = ArrayHelper::merge($defaults, $this->enables);

        $defaults = array('label' => Html::encode(Yii::$app->name), 'url' => 'javascript:;');
        $this->brandOptions = ArrayHelper::merge($defaults, $this->brandOptions);

        $defaults = array('items' => []);
        $this->menuOptions = ArrayHelper::merge($defaults, $this->menuOptions);
    }
}
