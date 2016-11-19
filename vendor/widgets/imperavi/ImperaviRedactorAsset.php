<?php

namespace cza\base\vendor\widgets\imperavi;

use Yii;

/**
 * Descriptions
 *
 *
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
class ImperaviRedactorAsset extends \yii\imperavi\ImperaviRedactorAsset {
    public function setLang($lang = 'en') {
        if ($lang != 'en')
            $this->js[] = 'lang/' . $lang . '.js';
        return $this;
    }

}
