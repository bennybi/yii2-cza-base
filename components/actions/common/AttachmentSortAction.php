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
 * AttachmentSortAction class file.
 */
class AttachmentSortAction extends Action {

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
    public function run($ids) {

        if (!empty($ids)) {
            $attachementClass = $this->attachementClass;
            $sortIds = explode(',', $ids);
            $count = count($sortIds);
            for ($i = 0; $i < $count; $i++) {
                $positon = $count - $i;
                $model = $attachementClass::findOne(['id' => $sortIds[$i]]);
                if ($model) {
                    $model->updateAttributes(['position' => $positon]);
                }
            }

            if ($this->onComplete) {
                return call_user_func($this->onComplete, $sortIds);
            }
        }

        $responseData = ResponseDatum::getSuccessDatum(['message' => Yii::t('cza', 'Operation completed successfully!')], $ids);
        return $this->controller->asJson($responseData);
    }

}
