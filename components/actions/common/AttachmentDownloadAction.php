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
 * AttachmentDownloadAction class file.
 */
class AttachmentDownloadAction extends Action {

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
        if(is_null($attachement)){
            return $this->controller->asJson(ResponseDatum::getErrorDatum(['message' => Yii::t('cza', 'Error: attachment not found!')], $id));
        }
        
        $file = $attachement->getStorePath();
        if(!file_exists($file)){
            return $this->controller->asJson(ResponseDatum::getErrorDatum(['message' => Yii::t('cza', 'Error: attachment not found!')], $id));
        }
        
        if (Yii::$app->request->isAjax) {
            $responseData = ResponseDatum::getSuccessDatum(['message' => Yii::t('cza', 'Operation completed successfully!')], $id);
            return $this->controller->asJson($responseData);
        }

        Yii::$app->response->sendFile($file);
    }

}
