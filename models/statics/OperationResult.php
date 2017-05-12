<?php

namespace cza\base\models\statics;


/**
 * Description of OperationResult
 *   return operation result status
 *
 * @author ben
 */
class OperationResult {

    CONST SUCCESS = "0000";
    CONST ERROR = "0001";
    CONST WARNING = "0002";
    CONST INFO = "0003";

    protected static $_types = array(
        self::SUCCESS => 'success',
        self::ERROR => 'error',
        self::WARNING => 'warning',
        self::INFO => 'info',
    );

    public static function getType($code) {
        return self::$_types[$code];
    }

}
