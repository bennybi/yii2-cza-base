<?php

namespace cza\base\widgets;

use Yii;
use yii\base\InvalidCallException;
use yii\base\Model;
use yii\helpers\Html;

/**
 * Entity Manager Widget
 * 
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
class EntityManager extends Widget {

    public $model;
    public $dataProvider;
    public $searchModel;
    
    public function getTabId() {
        if ($this->model) {
            return $this->model->formName() . '-manager-tabs';
        }
        return $this->className() . '-tabs';
    }
}
