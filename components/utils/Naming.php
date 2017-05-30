<?php

namespace cza\base\components\utils;

use Yii;
use yii\helpers\Url;
use yii\base\Exception;

/**
 * nameing utility
 *
 *
 * @author Ben Bi <bennybi@qq.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
class Naming extends \yii\base\Component {

    CONST UPPER = 1;
    CONST LOWER = 2;

    /**
     * usage example. CmsPageSort => cms-page-sort
     * @param type $name
     * @param type $splitor
     * @param type $to
     * @return type
     */
    public function toSplit($name, $splitor = '-', $to = self::LOWER) {
        switch ($to) {
            case self::UPPER:
                return strtoupper(preg_replace("/(.)([A-Z])/", "$1{$splitor}$2", $name));
            default:
                return strtolower(preg_replace("/(.)([A-Z])/", "$1{$splitor}$2", $name));
        }
    }

    public function shortClassName($obj) {
        $reflector = new \ReflectionClass($obj);
        return $reflector->getShortName();
    }

}
