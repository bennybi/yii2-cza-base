<?php

namespace cza\base\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * enhance model's functionality
 */
trait ModelTrait {

    protected $_data = [];

    /**
     * convert camel form/class name to 'id' name
     * eg. CmsPage -> cms-page
     * @return string
     */
    public function getCamel2IdFormName() {
        if (!isset($this->_data['camelFormName'])) {
            $this->_data['camelFormName'] = Inflector::camel2id($this->formName());
        }
        return $this->_data['camelFormName'];
    }

    public function getBaseFormName($isId = false) {
        $name = $this->getCamel2IdFormName() . '-base-form';
        return $isId ? "#" . $name : $name;
    }

    public function getPjaxName($isId = false) {
        $name = $this->getCamel2IdFormName() . '-pjax';
        return $isId ? "#" . $name : $name;
    }

    public function getPrefixName($name, $isId = false) {
        $pName = $this->getCamel2IdFormName() . "-{$name}";
        return $isId ? "#" . $pName : $pName;
    }

    /**
     * for message interactive
     * @param type $isId
     * @return type
     */
    public function getMessageName($isId = false) {
        $name = $this->getCamel2IdFormName() . '-message';
        return $isId ? "#" . $name : $name;
    }

    public function getDetailPjaxName($isId = false) {
        $name = $this->getCamel2IdFormName() . '-detail-pjax';
        return $isId ? "#" . $name : $name;
    }

    public function getFormAttributes() {
        return [
            $this->formName() => $this->attributes
        ];
    }

}
