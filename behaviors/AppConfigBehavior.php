<?php

/**
 * 
 * handle application settings
 * @author Ben Bi <bennybi@qq.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */

namespace cza\base\behaviors;

use Yii;
use yii\base\Behavior;
use yii\base\Event;

/**
 * Descriptions
 * 
 *
 * @author Ben Bi <bennybi@qq.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
class AppConfigBehavior extends Behavior {

    public function events() {
        $events = parent::events();
        $events[\yii\base\Application::EVENT_BEFORE_REQUEST] = 'beforeRequest';
        return $events;
    }

    public function beforeRequest($event) {
        if (Yii::$app->session) {
            Yii::$app->language = Yii::$app->session->get('user.language', Yii::$app->language);
        }
    }

}
