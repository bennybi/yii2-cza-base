<?php

namespace cza\base\widgets\ui\common\part;

use Yii;
use ArrayAccess;
use ArrayObject;
use ArrayIterator;
use ReflectionClass;
use yii\base\InvalidCallException;
use yii\base\Model;
use cza\base\widgets\Widget;

/**
 * Cza StdWidget
 * 
 * @author Ben Bi <bennybi@qq.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
class StdWidget extends Widget {

    public $content;

    public function init() {
        parent::init();
    }

}
