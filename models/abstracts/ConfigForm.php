<?php

namespace cza\base\models\abstracts;

use Yii;
use yii\base\Model;
use common\models\c2\entity\Config;
use yii\web\NotFoundHttpException;
use cza\base\models\ModelTrait;
use yii\helpers\Inflector;
use common\models\c2\statics\ConfigType;
use yii\base\ModelEvent;

/**
 * Form represents the model behind the search form about `common\models\c2\entity\CmsPage`.
 */
abstract class ConfigForm extends Model {

    /**
     * @event ModelEvent an event that is triggered before inserting a record.
     * You may set [[ModelEvent::isValid]] to be `false` to stop the insertion.
     */
    const EVENT_BEFORE_SAVE = 'beforeSave';

    /**
     * @event AfterSaveEvent an event that is triggered after a record is inserted.
     */
    const EVENT_AFTER_SAVE = 'afterSave';

    // extend from cza\base\models\abstracts\Config
    protected $_model = null;
    protected $_tips;

    use ModelTrait;

    public function init() {
        parent::init();
        if (is_null($this->model)) {
            throw new \yii\web\NotFoundHttpException("Model is required!");
        }
        $this->loadDefaultValues();
    }

    public function getId() {
        return $this->model->id;
    }

    public function loadDefaultValues() {
        if (!is_null($this->model)) {
            $this->attributes = $this->model->getDataToArray();
        }
    }

    public function save() {
        if (!($this->validate())) {
            return false;
        }

        if (!$this->beforeSave()) {
            return false;
        }

        $this->model->saveDataItems($this->attributes);
//        if (!$this->model->saveDataItems($this->attributes)) {
//            $this->addError('tips', \Yii::t('app.c2', "{s1} cannot be saved. \n Reason: {s2}", [
//                        's1' => 'Tips',
//                        's2' => print_r($this->model->getErrors(), true),
//            ]));
//            return false;
//        }

        $this->afterSave();

        return true;
    }

    public function beforeSave() {
        $event = new ModelEvent();
        $this->trigger(self::EVENT_BEFORE_SAVE, $event);

        return $event->isValid;
    }

    public function afterSave() {
        $event = new ModelEvent();
        $this->trigger(self::EVENT_AFTER_SAVE, $event);
    }

    public function getModel() {
        return $this->_model;
    }

    public function setModel($v) {
        $this->_model = $v;
    }

    public function getTips() {
        return $this->_tips;
    }

    public function setTips($v) {
        $this->_tips = $v;
    }

}
