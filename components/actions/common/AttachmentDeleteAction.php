<?php

namespace cza\base\components\actions\common;

use Yii;
use yii\base\Action;
use yii\helpers\FileHelper;
use yii\web\HttpException;
use yii\web\Response;
use cza\base\models\statics\ResponseDatum;

/**
 * handle upload upload images as attachemnts of the entity
 * AttachmentDeleteAction class file.
 */
class AttachmentDeleteAction extends Action {

    public $onComplete;
    public $attachementClass;

    /**
     * Initializes the action and ensures the temp path exists.
     */
    public function init() {
        parent::init();

        if (empty($this->attachementClass)) {
            throw new \yii\base\Exception("attachementClass is required!");
        }
    }

    /**
     * Runs the action.
     * This method displays the view requested by the user.
     * @throws HttpException if the view is invalid
     */
    public function run($id) {
        $attachementClass = $this->attachementClass;
        $attachement = $attachementClass::findOne(['id' => $id]);
        if ($attachement->delete()) {
            if ($this->onComplete) {
                return call_user_func($this->onComplete, $id);
            }
            $responseData = ResponseDatum::getSuccessDatum(['message' => Yii::t('cza', 'Operation completed successfully!')], $id);
        } else {
            $responseData = ResponseDatum::getErrorDatum(['message' => Yii::t('cza', 'Error: operation can not finish!!')], $id);
        }
        return $this->controller->asJson($responseData);
    }

}
