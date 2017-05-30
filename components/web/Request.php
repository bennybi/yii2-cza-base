<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace cza\base\components\web;

use Yii;

class Request extends \yii\web\Request {

    public $noCsrfRoutes = [];

    public function validateCsrfToken($clientSuppliedToken = null) {
        if (
                $this->enableCsrfValidation &&
                in_array(Yii::$app->getUrlManager()->parseRequest($this)[0], $this->noCsrfRoutes)
        ) {
            return true;
        }
        return parent::validateCsrfToken($clientSuppliedToken);
    }

}
