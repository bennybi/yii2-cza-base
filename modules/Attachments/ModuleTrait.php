<?php

namespace cza\base\modules\Attachments;

trait ModuleTrait
{
    /**
     * @var null|Module
     */
    private $_module = null;

    /**
     * @return null|Module
     * @throws \Exception
     */
    protected function getModule()
    {
        if ($this->_module == null) {
            $this->_module = \Yii::$app->getModule('attachments');
        }

        if (!$this->_module) {
            throw new \Exception("Yii2 attachment module not found, may be you didn't add it to your config?");
        }

        return $this->_module;
    }
}