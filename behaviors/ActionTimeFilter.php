<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace cza\base\behaviors;

/**
 * Description of ActionTimeFilter
 *
 * @author ben
 */
use Yii;
use yii\base\ActionFilter;

class ActionTimeFilter extends ActionFilter {

    private $_startTime;

    public function beforeAction($action) {
        $this->_startTime = microtime(true);
        return parent::beforeAction($action);
    }

    public function afterAction($action, $result) {
        $time = microtime(true) - $this->_startTime;
        Yii::trace("Action '{$action->uniqueId}' spent $time second.");
        return parent::afterAction($action, $result);
    }

}
