<?php

/*
 * Test Command
 * 
 * Usage: 
 * yii cza/test -m=hello
 */

namespace cza\base\modules\console\commands;

use Yii;
use yii\db\Connection;
use yii\db\Query;
use yii\di\Instance;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\console\Controller;

class TestController extends Controller {

    public $message;

    public function options($actionID) {
        return ['message'];
    }

    public function optionAliases() {
        return ['m' => 'message'];
    }

    public function actionIndex() {
        $this->stdout($this->message, Console::FG_YELLOW);
    }

// The command "yii example/create test" will call "actionCreate('test')"
    public function actionCreate($name) {
        ;
    }

    // The command "yii example/index city" will call "actionIndex('city', 'name')"
    // The command "yii example/index city id" will call "actionIndex('city', 'id')"
    public function actionZone($category, $order = 'name') {
        ;
    }

    // The command "yii example/add test" will call "actionAdd(['test'])"
    // The command "yii example/add test1,test2" will call "actionAdd(['test1', 'test2'])"
    public function actionAdd(array $name) {
        Yii::info(\yii\helpers\VarDumper::dumpAsString($name));
    }

}
