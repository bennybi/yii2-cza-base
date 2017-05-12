<?php

namespace cza\base\modules\console;

use yii\base\Module as BaseModule;

/**
 * CZA console module.
 * 
 * @author Ben Bi <jianbinbi@gmail.com>
 */
class Module extends BaseModule
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'cza\base\modules\console\commands';
}